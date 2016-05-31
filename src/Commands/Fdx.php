<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/6/1
 * Time: 上午1:12
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 */

namespace Fdx\Commands;

use FastD\Console\Command\Command;
use FastD\Console\IO\Input;
use FastD\Console\IO\Output;

class Fdx extends Command
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'fdx';
    }

    /**
     * @return void
     */
    public function configure()
    {
        // TODO: Implement configure() method.
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int
     */
    public function execute(Input $input, Output $output)
    {
        // TODO: Implement execute() method.
    }
}