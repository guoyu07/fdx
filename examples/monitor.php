<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use Fdx\Monitor;

include __DIR__ . '/../vendor/autoload.php';

$server = new Monitor('tcp://0.0.0.0:9530');

$server->start();