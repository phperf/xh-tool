<?php

namespace Phperf\XhTool\App;

use Yaoi\Command\Definition;
use Yaoi\Command\Option;
use Yaoi\Io\Content\Heading;

class Func extends ProfileCommand
{
    /**
     * @param Definition $definition
     * @param \stdClass|static $options
     * @throws \Exception
     */
    static function setUpDefinition(Definition $definition, $options)
    {
        parent::setUpDefinition($definition, $options);
        Option::cast($options->filter)->setIsUnnamed()->setIsRequired();
    }

    public function performAction()
    {
        $stats = $this->getStats();
        $rows = $stats->filter($stats->symbolStats, $this->filter);
        if (!$rows) {
            $this->response->error('Function not found: ' . $this->filter);
            return false;
        }
        $rows = $stats->getSlice($rows, 1);
        $funcStat = $rows[0];
        $func = $funcStat->name;
        $this->response->addContent(new Heading('Function'));
        $this->tableStats([$rows[0]], $stats->main);

        $rows = [];
        foreach ($funcStat->parents as $parent => $tmp) {
            $rows[$parent] = $stats->parentStats[$parent][$func];
        }
        if ($rows) {
            $stats->orderBy($rows);
            $this->response->addContent(new Heading('Parents'));
            $this->tableStats($rows, $stats->main);
        }

        $rows = [];
        foreach ($funcStat->children as $child => $tmp) {
            $rows[$child] = $stats->childStats[$func][$child];
        }
        if ($rows) {
            $stats->orderBy($rows);
            $this->response->addContent(new Heading('Children'));
            $this->tableStats($rows, $stats->main);
        }

    }

}