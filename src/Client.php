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
use FastD\Swoole\Client\Async\AsyncClient;
use FastD\Swoole\Client\Sync\SyncClient;

class Client
{
    const SYNC = 1;
    const ASYNC = 0;

    /**
     * 秒
     * @const int
     */
    const CACHE_TIME = 60;

    public $available = [];

    public $cache;

    protected $address;

    protected $mode;

    public function __construct($address, $isSync = Client::SYNC)
    {
        $this->address = $address;

        $this->mode = $isSync;

        $this->cache = __DIR__ . '/available.cache';
    }

    public function getAvailableServices()
    {
        if (!file_exists($this->cache) || time() - filemtime($this->cache) > static::CACHE_TIME) {
            $that = $this;
            $client = new SyncClient($this->address);
            $config = [];
            $client
                ->connect(function ($client) {
                    $client->send('it\'s me');
                })->receive(function ($client, $data) use ($that, &$config) {
                    $that->available = Json::decode($data);
                    file_put_contents($that->cache, '<?php return ' . var_export($this->available, true) . ';');
                    $config = $this->available;
                })
                ->resolve();
        } else {
            $config = include $this->cache;
        }

        return $config;
    }

    public function createClient()
    {
        $nodes = $this->getAvailableServices();
        $rand = array_rand($nodes['nodes']);
        $target = $nodes['nodes'][$rand];
        $uri = 'tcp://' . $target['host'] . ':' . $target['port'];

        switch ($this->mode) {
            case Client::ASYNC:
                $client = new AsyncClient($uri);
                break;
            case Client::SYNC:
            default:
                $client = new SyncClient($uri);
        }

        return $client;
    }

    public function call($service, array $arguments = [])
    {
        $client = $this->createClient();

        $result = [];

        $client
            ->connect(function ($client) use ($service, $arguments) {
                $client->send(Json::encode([
                    'service' => $service,
                    'arguments' => $arguments,
                ]));
            })
            ->receive(function ($client, $data) use (&$result) {
                $result = Json::decode($data);
            })
            ->resolve()
        ;

        return $result;
    }

    public function multiCall(array $services)
    {

    }
}