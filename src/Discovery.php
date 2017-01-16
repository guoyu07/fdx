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

/**
 * Class Discovery
 * @package Fdx
 */
class Discovery extends Tcp
{
    const SERVER_NAME = 'fds discovery';

    const NODE_KEY = 'nodes';

    /**
     * 启动多端口
     *
     * @param swoole_server_port|null $swoole
     * @return $this
     */
    public function bootstrap($swoole = null)
    {
        $address = 'tcp://' . $this->host . ':' . ((int)$this->port + 1);

        // share config
        $server = new Discovery('report', $address);
        $this->listen($server);

        parent::bootstrap($swoole);

        return $this;
    }

    /**
     * 连接数据存储载体，目前使用Redis
     *
     * @return Redis
     */
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

        // TODO 内部循环，超时没有数据，剔除异常服务端
        if ($server->port == $info['server_port']) {
            $data = Json::decode($data);
            print_r($data);
            $result = $redis->hSet(Discovery::NODE_KEY, $data['host'], json_encode($data));
            $length = $redis->hLen(Discovery::NODE_KEY);

            return Json::encode([
                'length' => $length,
                'status' => $result,
                'update_at' => time()
            ]);
        } else {
            $list = $redis->hGetAll(Discovery::NODE_KEY);
            foreach ($list as $key => $value) {
                $list[$key] = json_decode($value, true);
            }
            return Json::encode([
                'nodes' => $list,
                'length' => count($list),
            ]);
        }
    }

    /**
     * Please return swoole configuration array.
     *
     * @return array
     */
    public function configure()
    {
        return [
            'redis' => [
                'host' => '22.11.11.22',
                'port' => 6379
            ]
        ];
    }
}