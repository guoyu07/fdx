<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use Fdx\Discovery;

include __DIR__ . '/../vendor/autoload.php';

$server = new Discovery('discovery', 'tcp://0.0.0.0:9528');

$server->start();