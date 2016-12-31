<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */


include __DIR__ . '/../vendor/autoload.php';

use Fdx\Client;

$client = new Client('tcp://0.0.0.0:9529');

$length = 10000;
$start = microtime(true);
for ($i = 0; $i < $length; $i++) {
    $client->call('demo');
}
echo microtime(true) - $start;