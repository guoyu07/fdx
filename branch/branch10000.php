<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

use Fdx\FdxClient;

$client = new FdxClient('tcp://127.0.0.1:9527');

$result = $client->call('test.getAge');

echo $result . PHP_EOL;

$start = microtime(true);
/*for ($i = 0; $i < 10000; $i++) {

}
echo $i;*/

$client->close();
$end = microtime(true);

//echo "TC " . round($start - $end, 3) . PHP_EOL;