<?php

namespace Prooph\Snapshotter;

use Assert\Assertion;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\Snapshot\Adapter\Adapter;
use Prooph\EventStore\Snapshot\Snapshot;

/**
 * Class Snapshotter
 * @package Prooph\Snapshotter
 */
final class Snapshotter
{
    /**
     * @var Adapter
     */
    private $snapshotAdapter;

    /**
     * @var AggregateRepository[]
     *
     * key: aggregate type
     * value: aggregate repository
     */
    private $aggregateRepositories;

    /**
     * @param Adapter $snapshotAdapter
     */
    public function __construct(Adapter $snapshotAdapter, array $aggregateRepositories)
    {
        Assertion::allIsInstanceOf($aggregateRepositories, AggregateRepository::class);

        $this->snapshotAdapter = $snapshotAdapter;
        $this->aggregateRepositories = $aggregateRepositories;
    }

    /**
     * @param TakeSnapshot $command
     */
    public function __invoke(TakeSnapshot $command)
    {
        $repository = $this->aggregateRepositories[$command->aggregateType()];
        $aggregateRoot = $repository->getAggregateRoot($command->aggregateId());

        $this->snapshotAdapter->add(new Snapshot(
            AggregateType::fromAggregateRootClass($command->aggregateType()),
            $command->aggregateId(),
            $aggregateRoot,
            $command->version(),
            $command->createdAt()
        ));
    }
}
