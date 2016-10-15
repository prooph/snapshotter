<?php
/*
 * This file is part of prooph/snapshotter.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/02/15 - 03:52 PM
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
     * List of event names to take snapshot
     *
     * @var array
     */
    private $eventNames;

    /**
     * Shortcut to determine if event names available
     *
     * @var bool
     */
    private $hasEventNames;

    /**
     * @param CommandBus $commandBus
     * @param int $versionStep
     * @param array $eventNames
     */
    public function __construct(CommandBus $commandBus, $versionStep, array $eventNames = [])
    {
        Assertion::min($versionStep, 1);
        $this->commandBus = $commandBus;
        $this->versionStep = $versionStep;
        $this->eventNames = $eventNames;
        $this->hasEventNames = !empty($eventNames);
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

        /* @var $recordedEvent \Prooph\Common\Messaging\Message */
        foreach ($recordedEvents as $recordedEvent) {
            $doSnapshot = $recordedEvent->version() % $this->versionStep === 0;

            if (false === $doSnapshot && false === $this->hasEventNames) {
                continue;
            }
            $metadata = $recordedEvent->metadata();
            if (!isset($metadata['aggregate_type'], $metadata['aggregate_id'])
                || (false === $doSnapshot && !in_array($recordedEvent->messageName(), $this->eventNames, true))
            ) {
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
