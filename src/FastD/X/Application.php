<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/2/15
 * Time: 下午6:26
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\X;

use FastD\Swoole\Manager\ServerManager;
use FastD\Swoole\Server\Server;

/**
 * FDX Application bootstrap.
 *
 * Class Application
 *
 * @package FastD\X
 */
class Application
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * Application constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return ServerManager
     */
    public function getManager()
    {
        if (!isset($this->config['host']) || !isset($this->config['port'])) {
            throw new \RuntimeException('Undefined host or port.');
        }

        $server = Server::create($this->config['host'], $this->config['port']);

        unset($this->config['host'], $this->config['port']);

        $server->config($this->config);

        return (new ServerManager())->bindServer($server);
    }
}