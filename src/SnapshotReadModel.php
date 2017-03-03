<?php
/**
 * This file is part of the prooph/snapshotter.
 * (c) 2015-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\Snapshotter;

use ArrayIterator;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateTranslator;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventStore\Projection\ReadModel;
use Prooph\SnapshotStore\Snapshot;
use Prooph\SnapshotStore\SnapshotStore;

final class SnapshotReadModel implements ReadModel
{
    /**
     * @var AggregateRepository
     */
    private $aggregateRepository;

    /**
     * @var AggregateTranslator
     */
    private $aggregateTranslator;

    /**
     * @var array
     */
    private $aggregateCache = [];

    /**
     * @var SnapshotStore
     */
    private $snapshotStore;

    /**
     * @var AggregateType[]
     */
    private $aggregateTypes;

    public function __construct(
        AggregateRepository $aggregateRepository,
        AggregateTranslator $aggregateTranslator,
        SnapshotStore $snapshotStore,
        array $aggregateTypes
    ) {
        $this->aggregateRepository = $aggregateRepository;
        $this->aggregateTranslator = $aggregateTranslator;
        $this->aggregateTypes = $aggregateTypes;
        $this->snapshotStore = $snapshotStore;
    }

    public function stack(string $operation, ...$events): void
    {
        $event = $events[0];

        if (! $event instanceof AggregateChanged) {
            throw new \RuntimeException(get_class($this) . ' can only handle events of type ' . AggregateChanged::class);
        }

        $aggregateId = $event->aggregateId();

        if (! isset($this->aggregateCache[$aggregateId])) {
            $aggregateRoot = $this->aggregateRepository->getAggregateRoot($aggregateId);

            if (! $aggregateRoot) {
                // this happens when you have multiple aggregate types in a single stream
                return;
            }

            $this->aggregateCache[$aggregateId] = $aggregateRoot;
        }

        $this->aggregateTranslator->replayStreamEvents(
            $this->aggregateCache[$aggregateId],
            new ArrayIterator([$event])
        );
    }

    public function persist(): void
    {
        foreach ($this->aggregateCache as $aggregateRoot) {
            $this->snapshotStore->save(new Snapshot(
                (string) AggregateType::fromAggregateRoot($aggregateRoot),
                $this->aggregateTranslator->extractAggregateId($aggregateRoot),
                $aggregateRoot,
                $this->aggregateTranslator->extractAggregateVersion($aggregateRoot),
                new \DateTimeImmutable('now', new \DateTimeZone('UTC'))
            ));
        }

        $this->aggregateCache = [];
    }

    public function init(): void
    {
        throw new \BadMethodCallException('Initializing a snapshot read model is not supported');
    }

    public function isInitialized(): bool
    {
        return true;
    }

    public function reset(): void
    {
        foreach ($this->aggregateTypes as $aggregateType) {
            $this->snapshotStore->removeAll((string) $aggregateType);
        }
    }

    public function delete(): void
    {
        throw new \BadMethodCallException('Deleting a snapshot read model is not supported');
    }
}
