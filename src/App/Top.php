<?php

namespace Phperf\XhTool\App;

use Yaoi\Command;

class Top extends ProfileCommand
{
    /**
     * @param Command\Definition $definition
     * @param \stdClass|static $options
     */
    static function setUpDefinition(Command\Definition $definition, $options)
    {
        parent::setUpDefinition($definition, $options);

    }


    public function performAction()
    {
        $stats = $this->getStats();
        $rows = $stats->symbolStats;
        if ($this->filter) {
            $rows = $stats->filter($rows, $this->filter);
        }
        if ($this->limit) {
            $rows = $stats->getSlice($rows, $this->limit);
        }
        $this->tableStats($rows, $stats->main);
    }
}