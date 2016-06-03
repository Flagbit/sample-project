#!/usr/bin/env php
<?php

namespace LizardsAndPumpkins;

use League\CLImate\CLImate;
use LizardsAndPumpkins\Messaging\Queue;
use LizardsAndPumpkins\Util\BaseCliCommand;
use LizardsAndPumpkins\Util\Factory\CommonFactory;
use LizardsAndPumpkins\Util\Factory\MasterFactory;
use LizardsAndPumpkins\Util\Factory\SampleMasterFactory;
use LizardsAndPumpkins\Util\Factory\DemoProjectFactory;

require_once __DIR__ . '/../../../vendor/autoload.php';

class ReportQueueCount extends BaseCliCommand
{
    /**
     * @var SampleMasterFactory
     */
    private $factory;

    private function __construct(MasterFactory $factory, CLImate $climate)
    {
        $this->factory = $factory;
        $this->setCLImate($climate);
    }

    /**
     * @return ReportQueueCount
     */
    public static function bootstrap()
    {
        $factory = new SampleMasterFactory();
        $factory->register(new CommonFactory());
        $factory->register(new DemoProjectFactory());

        return new self($factory, new CLImate());
    }

    protected function execute(CLImate $climate)
    {
        $tableData = $this->formatTableData($this->factory->getCommandQueue(), $this->factory->getEventQueue());
        $climate->table($tableData);
    }

    /**
     * @param Queue $commandQueue
     * @param Queue $eventQueue
     * @return string[]
     */
    private function formatTableData(Queue $commandQueue, Queue $eventQueue)
    {
        return [
            [
                'Queue' => 'Command',
                'Count' => sprintf('%10d', $commandQueue->count())
            ],
            [
                'Queue' => 'Event',
                'Count' => sprintf('%10d', $eventQueue->count())
            ],
        ];
    }
}

ReportQueueCount::bootstrap()->run();
