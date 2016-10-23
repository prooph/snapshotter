<?php
/**
 * This file is part of the prooph/snapshotter.
 * (c) 2015-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
    public function __construct(CommandBus $commandBus, int $versionStep, array $eventNames = [])
    {
        Assertion::min($versionStep, 1);
        $this->commandBus = $commandBus;
        $this->versionStep = $versionStep;
        $this->eventNames = $eventNames;
        $this->hasEventNames = ! empty($eventNames);
    }

    public function setUp(EventStore $eventStore): void
    {
        $eventStore->getActionEventEmitter()->attachListener('commit.post', [$this, 'onEventStoreCommitPost'], -1000);
    }

    /**
     * Take snapshots on event-store::commit.post
     */
    public function onEventStoreCommitPost(ActionEvent $actionEvent): void
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
            if (! isset($metadata['aggregate_type'], $metadata['aggregate_id'])
                || (false === $doSnapshot && ! in_array($recordedEvent->messageName(), $this->eventNames, true))
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
