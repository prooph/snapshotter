<?php
/*
 * This file is part of prooph/snapshotter.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/02/15 - 04:12 PM
 */

namespace Prooph\Snapshotter;

use Assert\Assertion;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\Snapshot\Snapshot;
use Prooph\EventStore\Snapshot\SnapshotStore;

/**
 * Class Snapshotter
 * @package Prooph\Snapshotter
 */
final class Snapshotter
{
    /**
     * @var SnapshotStore
     */
    private $snapshotStore;

    /**
     * @var AggregateRepository[]
     *
     * key: aggregate type
     * value: aggregate repository
     */
    private $aggregateRepositories;

    /**
     * @var bool
     */
    private $clearIdentityMap;

    /**
     * @param SnapshotStore $snapshotStore
     * @param AggregateRepository[] $aggregateRepositories
     */
    public function __construct(SnapshotStore $snapshotStore, array $aggregateRepositories, $clearIdentityMap)
    {
        Assertion::allIsInstanceOf($aggregateRepositories, AggregateRepository::class);
        Assertion::boolean($clearIdentityMap);

        $this->snapshotStore = $snapshotStore;
        $this->aggregateRepositories = $aggregateRepositories;
        $this->clearIdentityMap = $clearIdentityMap;
    }

    /**
     * @param TakeSnapshot $command
     */
    public function __invoke(TakeSnapshot $command)
    {
        $aggregateType = $command->aggregateType();

        if (!isset($this->aggregateRepositories[$aggregateType])) {
            throw new Exception\RuntimeException(sprintf(
                'No repository for aggregate type %s configured',
                $command->aggregateType()
            ));
        }

        $repository = $this->aggregateRepositories[$aggregateType];
        $aggregateRoot = $repository->getAggregateRoot($command->aggregateId());

        $this->snapshotStore->save(new Snapshot(
            AggregateType::fromAggregateRootClass($aggregateType),
            $command->aggregateId(),
            $aggregateRoot,
            $repository->extractVersion($aggregateRoot),
            $command->createdAt()
        ));

        if ($this->clearIdentityMap) {
            $repository->clearIdentityMap();
        }
    }
}
