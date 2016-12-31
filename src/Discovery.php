<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace Fdx;


use FastD\Packet\Json;
use FastD\Swoole\Server\Tcp;
use Redis;
use swoole_server;
use swoole_server_port;

class Discovery extends Tcp
{
    const SERVER_NAME = 'fds discovery';

    public function bootstrap(swoole_server_port $swoole = null)
    {
        $address = 'tcp://' . $this->host . ':' . ((int)$this->port + 1);

        // share config
        $server = new Discovery($address);
        $server->configure($this->config);
        $this->listen($server);

        $bootstrap = parent::bootstrap($swoole);

        return $bootstrap;
    }

    protected function connectToRedis()
    {
        $redis = new Redis();

        $config = $this->config['redis'];

        $redis->pconnect($config['host'], $config["port"], isset($config['timeout']) ? $config['timeout'] : 3);

        if (isset($config['auth'])) {
            $redis->auth($config['auth']);
        }

        if (isset($config['dbindex'])) {
            $redis->select($config['dbindex']);
        }

        return $redis;
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
        $info = $server->connection_info($fd, $from_id);
        $redis = $this->connectToRedis();

        if ($server->port == $info['server_port']) {
            $data = Json::decode($data);
            $result = $redis->hSet('node', $data['host'], json_encode($data));
            $length = $redis->hLen('node');

            return Json::encode([
                'length' => $length,
                'status' => $result,
                'update_at' => time()
            ]);
        } else {
            $list = $redis->hGetAll('node');
            return Json::encode([
                'node' => $list,
                'length' => count($list),
            ]);
        }
    }
}