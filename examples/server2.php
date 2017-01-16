<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

use Fdx\Server;

class Server2 extends Server {
    const SERVER_NAME =  'fds 2';
}

$server = new Server2('tcp://0.0.0.0:9533');
$server->withDiscovery([
    'tcp://127.0.0.1:9528'
]);
$server->withService('demo', function () {
    return [
        'foo' => 'server2'
    ];
});

$server->start();