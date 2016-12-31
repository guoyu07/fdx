<?php

namespace Fdx;
use FastD\Packet\Json;
use FastD\Swoole\Client\Sync\SyncClient;
use FastD\Swoole\Server\Tcp;
use swoole_server;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

class Server extends Tcp
{
    const SERVER_NAME = 'fds rpc';

    protected $services;

    protected $discoveries;

    public function withService($name, $callback)
    {
        $this->services[$name] = $callback;

        return $this;
    }

    public function withDiscovery(array $servers)
    {
        $this->discoveries = $servers;

        return $this;
    }

    protected function reported()
    {
        $that = $this;
        $servers = $this->discoveries;
        $process = new \swoole_process(function (\swoole_process $process) use ($servers, $that) {
            process_rename(static::SERVER_NAME . ' reporter');
            while (true) {
                $ip = get_local_ip();
                foreach ($servers as $server) {
                    try {
                        $client = new SyncClient($server);
                        $client
                            ->connect(function ($client) use ($ip) {
                                $client->send(Json::encode([
                                    'service'   => static::SERVER_NAME,
                                    'host'      => $ip,
                                    'time'      => time()
                                ]));
                            })
                            ->receive(function ($client, $data) {
                                print_r(Json::decode($data));
                                $client->close();
                            })
                            ->resolve()
                        ;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                sleep(10);
            }
        });
        $this->getSwoole()->addProcess($process);
    }

    public function bootstrap(\swoole_server_port $swoole = null)
    {
        parent::bootstrap();

        if (!empty($this->discoveries)) {
            $this->reported();
        }
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param $data
     * @param $from_id
     * @return mixed
     */
    public function doWork(swoole_server $server, $fd, $data, $from_id)
    {
        $data = Json::decode($data);
        if (!isset($data['service'])) {
            return Json::encode([
                'code' => -1,
                'msg' => 'Unable service',
            ]);
        }
        if (!isset($this->services[$data['service']])) {
            return Json::encode([
                'code' => -2,
                'msg' => 'Undefined service ' . $data['service']
            ]);
        }

        $service = $this->services[$data['service']];
        $response = call_user_func_array($service, (isset($data['data']) && is_array($data['data'])) ? $data['data'] : []);
        return Json::encode($response);
    }
}