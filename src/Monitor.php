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
use FastD\Http\SwooleServerRequest;
use FastD\Swoole\Server\Http;

class Monitor extends Http
{
    const SERVER_NAME = 'fds monitor';

    /**
     * @param SwooleServerRequest $request
     * @return Response
     */
    public function doRequest(SwooleServerRequest $request)
    {
        // TODO: Implement doRequest() method.
    }
}