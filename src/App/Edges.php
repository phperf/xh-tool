<?php

namespace Phperf\XhTool\App;

use Phperf\XhTool\Stat;
use Phperf\XhTool\Stats;

class Edges extends ProfileCommand
{
    public function performAction()
    {
        $data = $this->getProfileData();
        $rows = array();
        $main = null;
        foreach ($data as $key => $item) {
            $stat = new Stat();
            $stat->name = $key;
            $stat->count = $item['ct'];
            $stat->wallTime = $item['wt'];
            if (isset($item['cpu'])) {
                $stat->cpuTime = $item['cpu'];
            }
            if (isset($item['mu'])) {
                $stat->memoryUsage = $item['mu'];
            }
            if (isset($item['pmu'])) {
                $stat->peakMemoryUsage = $item['pmu'];
            }

            if ($key === 'main()') {
                $main = $stat;
            }

            $rows[] = $stat;
        }
        $stats = new Stats();
        $names = Stat::names();
        $order = $this->order ? $this->order : $names->ownTime;
        $stats->orderBy = $order;
        $stats->orderBy($rows);

        if ($this->filter) {
            $rows = $stats->filter($rows, $this->filter);
        }
        if ($this->limit) {
            $rows = $stats->getSlice($rows, $this->limit);
        }
        $this->tableStats($rows, $main);
    }
}