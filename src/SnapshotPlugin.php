<?php
/**
 * This file is part of the prooph/snapshotter.
 * (c) 2015-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prooph\Snapshotter;

use Assert\Assertion;
use Prooph\Common\Event\ActionEvent;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Plugin\Plugin;
use Prooph\ServiceBus\CommandBus;

/**
 * Class SnapshotPlugin
 * @package Prooph\Snapshotter
 */
final class SnapshotPlugin implements Plugin
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var int
     */
    private $versionStep;

    /**
     * @param CommandBus $commandBus
     * @param int $versionStep
     */
    public function __construct(CommandBus $commandBus, $versionStep)
    {
        Assertion::min($versionStep, 1);
        $this->commandBus = $commandBus;
        $this->versionStep = $versionStep;
    }

    /**
     * @param EventStore $eventStore
     * @return void
     */
    public function setUp(EventStore $eventStore)
    {
        $eventStore->getActionEventEmitter()->attachListener('commit.post', [$this, 'onEventStoreCommitPost'], -1000);
    }

    /**
     * Take snapshots on event-store::commit.post
     *
     * @param ActionEvent $actionEvent
     */
    public function onEventStoreCommitPost(ActionEvent $actionEvent)
    {
        $recordedEvents = $actionEvent->getParam('recordedEvents', new \ArrayIterator());

        $snapshots = [];

        foreach ($recordedEvents as $recordedEvent) {
            if ($recordedEvent->version() % $this->versionStep !== 0) {
                continue;
            }
            $metadata = $recordedEvent->metadata();
            if (!isset($metadata['aggregate_type']) || !isset($metadata['aggregate_id'])) {
                continue;
            }
            $snapshots[$metadata['aggregate_type']][] = $metadata['aggregate_id'];
        }

        foreach ($snapshots as $aggregateType => $aggregateIds) {
            foreach ($aggregateIds as $aggregateId) {
                $command = TakeSnapshot::withData($aggregateType, $aggregateId);
                $this->commandBus->dispatch($command);
            }
        }
    }
}
