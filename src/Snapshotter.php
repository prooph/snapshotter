<?php
/**
 * This file is part of the prooph/snapshotter.
 * (c) 2015-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\Snapshotter;

use Amp\Loop;
use Generator;
use Prooph\EventSourcing\Aggregate\AggregateTranslator;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\Aggregate\AsyncAggregateRepository;
use Prooph\SnapshotStore\Snapshot;
use Prooph\SnapshotStore\SnapshotStore;

class Snapshotter
{
    /** @var AsyncAggregateRepository */
    private $aggregateRepository;
    /** @var AggregateTranslator */
    private $aggregateTranslator;
    /** @var AggregateType */
    private $aggregateType;
    /** @var SnapshotStore */
    private $snapshotStore;
    /** @var string[] */
    private $aggregateIds = [];
    /** @var int */
    private $persistBatchSize;
    /** @var int */
    private $currentBatchSize = 0;
    /** @var int */
    private $lastEventNumber = 0;

    public function __construct(
        AsyncAggregateRepository $aggregateRepository,
        AggregateTranslator $aggregateTranslator,
        AggregateType $aggregateType,
        SnapshotStore $snapshotStore,
        int $persistBatchSize = 1000
    ) {
        $this->aggregateRepository = $aggregateRepository;
        $this->aggregateTranslator = $aggregateTranslator;
        $this->aggregateType = $aggregateType;
        $this->snapshotStore = $snapshotStore;
        $this->persistBatchSize = $persistBatchSize;
    }

    public function createSnapshotFor(string $aggregateId, int $lastEventNumber): void
    {
        $this->aggregateIds[] = $aggregateId;

        if (++$this->currentBatchSize >= $this->persistBatchSize) {
            $this->persistSnapshots($lastEventNumber);
        }
    }

    protected function persistSnapshots(int $lastEventNumber): void
    {
        $aggregateIds = \array_unique($this->aggregateIds);
        $this->aggregateIds = [];

        Loop::defer(function () use ($aggregateIds, $lastEventNumber): Generator {
            foreach (\array_unique($this->aggregateIds) as $aggregateId) {
                $aggregateRoot = yield $this->aggregateRepository->getAggregateRoot($aggregateId);

                if (null === $aggregateRoot) {
                    continue;
                }

                $snapshot = new Snapshot(
                    $this->aggregateType->typeFromAggregate($aggregateRoot),
                    $aggregateId,
                    $aggregateRoot,
                    $this->aggregateTranslator->extractExpectedVersion($aggregateRoot),
                    new \DateTimeImmutable('now', new \DateTimeZone('UTC'))
                );

                $this->snapshotStore->save($snapshot); // @todo allow async snapshot stores
            }

            $this->lastEventNumber = $lastEventNumber;
        });
    }

    public function lastEventNumber(): int
    {
        return $this->lastEventNumber;
    }
}
