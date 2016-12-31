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

/**
 * Class Server
 * @package Fdx
 */
class Server extends Tcp
{
    const SERVER_NAME = 'fds rpc';

    /**
     * @var array
     */
    protected $services;

    /**
     * @var array
     */
    protected $discoveries;

    /**
     * @param $name
     * @param $callback
     * @return $this
     */
    public function withService($name, $callback)
    {
        $this->services[$name] = $callback;

        return $this;
    }

    /**
     * @param array $servers
     * @return $this
     */
    public function withDiscovery(array $servers)
    {
        $this->discoveries = $servers;

        return $this;
    }

    /**
     * 上报服务器状态数据
     *
     * @return void
     */
    protected function reported()
    {
        $that = $this;
        $servers = $this->discoveries;
        $swoole = $this->getSwoole();
        $process = new \swoole_process(function (\swoole_process $process) use ($servers, $that, $swoole) {
            process_rename(static::SERVER_NAME . ' reporter');
            while (true) {
                $ip = get_local_ip();
                foreach ($servers as $server) {
                    try {
                        $client = new SyncClient($server);
                        $client
                            ->connect(function ($client) use ($ip, $swoole, $that) {
                                $client->send(Json::encode([
                                    'service'   => static::SERVER_NAME,
                                    'pid'       => $swoole->master_pid,
                                    'sock'      => $that->getSockType(),
                                    'host'      => $ip,
                                    'port'      => $that->getPort(),
                                    'stats'     => $swoole->stats(),
                                    'error'     => $swoole->getLastError(),
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

    /**
     * 初始化，添加进程到server中
     *
     * @param \swoole_server_port|null $swoole
     * @return $this
     */
    public function bootstrap(\swoole_server_port $swoole = null)
    {
        parent::bootstrap();

        if (!empty($this->discoveries)) {
            $this->reported();
        }

        return $this;
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
        $response = call_user_func_array($service, (isset($data['arguments']) && is_array($data['arguments'])) ? $data['arguments'] : []);
        return Json::encode($response);
    }
}