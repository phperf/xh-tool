<?php

namespace Phperf\XhTool;

class Stat
{
    /** @var string[] */
    public $parents = [];

    /** @var string[] */
    public $children = [];

    /** @var string */
    public $name;
    /** @var int */
    public $wallTime;

    /** @var int */
    public $count;

    /** @var int */
    public $childrenTime;

    /** @var int */
    public $ownTime;

    /**
     * @return NameMirror|static
     */
    static public function names()
    {
        return new NameMirror();
    }
}