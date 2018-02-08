<?php

namespace Phperf\XhTool\App;

use Phperf\XhTool\Formatter;
use Yaoi\Command;
use Yaoi\Io\Content\Heading;

class Info extends ProfileCommand
{
    /**
     * @param Command\Definition $definition
     * @param \stdClass|static $options
     */
    static function setUpDefinition(Command\Definition $definition, $options)
    {
        parent::setUpDefinition($definition, $options);
        unset($options->limit);
        unset($options->filter);
        unset($options->order);
        unset($options->stripNesting);
    }


    public function performAction()
    {
        $profileData = $this->getProfileData();
        $stats = $this->getStats();
        $this->response->addContent('Nodes: ' . count($profileData));
        $this->response->addContent('Functions: ' . count($stats->symbolStats));
        $this->response->addContent('Calls: ' . Formatter::count($stats->calls));

        $this->response->addContent(new Heading('Total'));
        $this->tableStats([$stats->main], $stats->main);
    }
}