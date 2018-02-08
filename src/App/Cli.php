<?php

namespace Phperf\XhTool\App;


use Yaoi\Command\Application;
use Yaoi\Command\Definition;

class Cli extends Application
{
    public $info;
    public $top;
    public $func;

    /**
     * @param Definition $definition
     * @param \stdClass|static $commandDefinitions
     */
    static function setUpCommands(Definition $definition, $commandDefinitions)
    {
        $definition->version = 'v1.0.0';
        $definition->name = 'xh-tool';
        $definition->description = 'XHPROF profile viewer';

        $commandDefinitions->info = Info::definition();
        $commandDefinitions->top = Top::definition();
        $commandDefinitions->func = Func::definition();
    }
}