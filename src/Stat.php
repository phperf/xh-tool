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
    public $count;

    /** @var int */
    public $wallTime;

    /** @var int */
    public $childrenTime;

    /** @var int */
    public $ownTime;


    /** @var int */
    public $cpuTime;

    /** @var int */
    public $childrenCpuTime;

    /** @var int */
    public $ownCpuTime;


    /** @var int */
    public $memoryUsage;

    /** @var int */
    public $peakMemoryUsage;

    /** @var int */
    public $peakMemoryShift;

    /**
     * @return NameMirror|static
     */
    static public function names()
    {
        return new NameMirror();
    }
}