<?php

namespace Phperf\XhTool;

class Stats
{
    public $stripNesting;

    public $orderBy;

    /** @var Stat[] */
    public $symbolStats = [];

    /** @var Stat[][] parent -> child -> stat */
    public $childStats = [];

    /** @var Stat[][] parent -> child -> stat */
    public $parentStats = [];

    /** @var Stat */
    public $main;

    /** @var int */
    public $calls = 0;

    public function addData($profileData)
    {
        foreach ($profileData as $key => $item) {
            /**
             * [ct] => 1
             * [wt] => 3
             * [cpu] => 4
             * [mu] => 848
             * [pmu] => 0
             */

            $keyParts = explode('==>', $key);

            $item['cpu'] = isset($item['cpu']) ? $item['cpu'] : 0;
            $item['mu'] = isset($item['mu']) ? $item['mu'] : 0;
            $item['pmu'] = isset($item['pmu']) ? $item['pmu'] : 0;

            if (count($keyParts) !== 2) {
                if ($key === 'main()') {
                    if (!isset($this->symbolStats[$key])) {
                        $main = new Stat();
                        $main->name = $key;
                        $this->symbolStats[$key] = $main;
                    } else {
                        $main = $this->symbolStats[$key];
                    }

                    $main->cpuTime += $item['cpu'];
                    $main->ownCpuTime += $item['cpu'];
                    $main->wallTime += $item['wt'];
                    $main->ownTime += $item['wt'];
                    $main->count += $item['ct'];
                    $main->memoryUsage = $item['mu'];
                    $main->peakMemoryUsage += $item['pmu'];
                    $main->peakMemoryShift += $item['pmu'];

                    $this->main = $main;
                    $this->calls += $item['ct'];
                }
                continue;
            } else {
                $parent = $keyParts[0];
                $child = $keyParts[1];
            }

            $childNesting = false;
            if ($this->stripNesting) {
                $tmp = explode('@', $parent, 2);
                $parent = $tmp[0];
                $tmp = explode('@', $child, 2);
                $child = $tmp[0];
                $childNesting = isset($tmp[1]);
            }

            if (!isset($this->symbolStats[$parent])) {
                $parentStat = new Stat();
                $parentStat->name = $parent;
                $parentStat->children = [$child => $item['ct']];

                $this->symbolStats[$parent] = $parentStat;
            } else {
                $parentStat = $this->symbolStats[$parent];
                if (isset($parentStat->children[$child])) {
                    $parentStat->children[$child] += $item['ct'];
                } else {
                    $parentStat->children[$child] = $item['ct'];
                }
            }

            if (!isset($this->symbolStats[$child])) {
                $childStat = new Stat();
                $childStat->name = $child;
                $childStat->parents = [$parent => $item['ct']];

                $this->symbolStats[$child] = $childStat;
            } else {
                $childStat = $this->symbolStats[$child];
                if (isset($childStat->parents[$parent])) {
                    $childStat->parents[$parent] += $item['ct'];
                } else {
                    $childStat->parents[$parent] = $item['ct'];
                }
            }

            $stat = new Stat();
            $stat->name = $child;
            $stat->wallTime = $item['wt'];
            $stat->cpuTime = $item['cpu'];
            $stat->count = $item['ct'];
            $stat->memoryUsage = $item['mu'];
            $stat->peakMemoryUsage = $item['pmu'];

            $this->childStats[$parent][$child] = $stat;

            $stat = new Stat();
            $stat->name = $parent;
            $stat->wallTime = $item['wt'];
            $stat->cpuTime = $item['cpu'];
            $stat->count = $item['ct'];
            $stat->memoryUsage = $item['mu'];
            $stat->peakMemoryUsage = $item['pmu'];
            $this->parentStats[$parent][$child] = $stat;


            if (!$childNesting) {
                $childStat->wallTime += $item['wt'];
                $childStat->cpuTime += $item['cpu'];
            }
            $childStat->ownTime += $item['wt'];
            $childStat->ownCpuTime += $item['cpu'];
            $childStat->count += $item['ct'];
            $childStat->memoryUsage += $item['mu'];
            $childStat->peakMemoryUsage += $item['pmu'];
            $childStat->peakMemoryShift += $item['pmu'];


            $parentStat->ownTime -= $item['wt'];
            $parentStat->childrenTime += $item['wt'];
            $parentStat->ownCpuTime -= $item['cpu'];
            //$parentStat->memoryUsage += $item['mu'];
            $parentStat->childrenCpuTime += $item['cpu'];
            $parentStat->peakMemoryShift -= $item['pmu'];

            $this->calls += $item['ct'];
        }
        return $this;
    }

    public function orderBy(&$data)
    {
        $field = $this->orderBy;
        $one = false;
        $percent = false;
        if (substr($field, -1) === '1') {
            $field = substr($field, 0, -1);
            $one = true;
        } elseif (substr($field, -1) === '%') {
            $field = substr($field, 0, -1);
            $percent = Stat::names()->wallTime;
        }
        uasort($data, function (Stat $a, Stat $b) use ($field, $one, $percent) {
            if ($one) {
                return $a->$field / $a->count < $b->$field / $b->count;
            } elseif ($percent) {
                return $a->$field / $this->main->$percent < $b->$field / $this->main->$percent;
            } else {
                return $a->$field < $b->$field;
            }
        });
        return $this;
    }

    /**
     * @param Stat[] $data
     * @param string $regex
     * @return Stat[]
     */
    public function filter($data, $regex)
    {
        $result = [];
        foreach ($data as $stat) {
            if (preg_match('/' . $regex . '/i', $stat->name)) {
                $result[] = $stat;
            }
        }
        return $result;
    }

    /**
     * @param Stat[] $data
     * @param int $limit
     * @param int $offset
     * @return Stat[]
     */
    public function getSlice($data, $limit = 20, $offset = 0)
    {
        return array_slice($data, $offset, $limit, true);
    }
}