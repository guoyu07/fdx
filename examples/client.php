<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Packet\Json;
use Fdx\Client;

$client = new Client('tcp://0.0.0.0:9527', SWOOLE_SOCK_TCP);

$client
    ->connect(function ($client) {
        $client->send(Json::encode([
            'service' => 'demo',
        ]));
    })
    ->receive(function ($client, $data) {
        print_r(Json::decode($data));
    })
    ->error(function ($client) {
        print_r($client);
    })
    ->close(function ($client) {})
    ->resolve()
;