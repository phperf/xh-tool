<?php

namespace Phperf\XhTool\App;

use Phperf\XhTool\Formatter;
use Phperf\XhTool\Stat;
use Phperf\XhTool\Stats;
use Yaoi\Command;
use Yaoi\Io\Content\Rows;
use Yaoi\Rows\Processor;

abstract class ProfileCommand extends Command
{
    public $profile;
    public $order;
    public $limit;
    public $filter;
    public $stripNesting;

    /**
     * @param Command\Definition $definition
     * @param \stdClass|static $options
     */
    static function setUpDefinition(Command\Definition $definition, $options)
    {
        $options->profile = Command\Option::create()->setIsUnnamed()->setIsRequired()
            ->setDescription('Path to XHPROF hierarchical profile');

        $options->stripNesting = Command\Option::create()->setDescription('Strip @N for nested calls');
        $options->limit = Command\Option::create()->setType()
            ->setDescription('Number of rows in result, default no limit');
        $options->filter = Command\Option::create()->setType()
            ->setDescription('Case-insensitive regex to filter by function name, '
                . 'example: "process$", "swaggest", "^MyNs\\\\MyClass\\\\MyMethod$"');

        $names = Stat::names();
        $options->order = Command\Option::create()->setType()
            ->setEnum(
                $names->name,
                $names->wallTime,
                $names->wallTime . '1',
                $names->wallTime . '%',
                $names->ownTime,
                $names->ownTime . '1',
                $names->ownTime . '%',
                $names->count)
            ->setDescription('Order by field, default: ' . $names->ownTime);

    }

    protected function getProfileData()
    {
        $profileData = unserialize(file_get_contents($this->profile));
        return $profileData;
    }

    protected function getStats()
    {
        $stats = new Stats();
        $stats->stripNesting = $this->stripNesting;

        $stats->addData($this->getProfileData());

        $names = Stat::names();
        $order = $this->order ? $this->order : $names->ownTime;
        $stats->orderBy = $order;
        $stats->orderBy($stats->symbolStats);
        return $stats;
    }

    /**
     * @param Stat[] $rows
     * @param Stat $main
     */
    protected function tableStats($rows, $main)
    {
        $names = Stat::names();
        $mainWt = 100 / $main->wallTime;
        $this->response->addContent(
            new Rows(
                (new Processor(
                    $rows
                ))->map(function (Stat $item) use ($names, $mainWt) {
                    $row = [
                        $names->name => $item->name,
                        $names->wallTime => Formatter::timeFromNs($item->wallTime),
                        $names->wallTime . '%' => round($item->wallTime * $mainWt, 2),
                        $names->wallTime . '1' => Formatter::timeFromNs(round($item->wallTime / $item->count, 1)),
                    ];
                    if (null !== $item->ownTime) {
                        $row[$names->ownTime] = Formatter::timeFromNs($item->ownTime);
                        $row[$names->ownTime . '%'] = round($item->ownTime * $mainWt, 2);
                        $row[$names->ownTime . '1'] = Formatter::timeFromNs(round($item->ownTime / $item->count, 1));
                    }

                    $row[$names->count] = Formatter::count($item->count);
                    return $row;
                })
            )
        );
    }

}