<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace Fdx;


use FastD\Http\Response;
use FastD\Http\ServerRequest;
use FastD\Http\SwooleServerRequest;
use FastD\Swoole\Server\Http;

class Monitor extends Http
{
    const SERVER_NAME = 'fds monitor';

    /**
     * @param SwooleServerRequest $request
     * @return Response
     */
    public function doRequest(ServerRequest $request)
    {
        // TODO: Implement doRequest() method.
    }

    /**
     * Please return swoole configuration array.
     *
     * @return array
     */
    public function configure()
    {
        // TODO: Implement configure() method.
    }
}