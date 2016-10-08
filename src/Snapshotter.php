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
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\Snapshot\Snapshot;
use Prooph\EventStore\Snapshot\SnapshotStore;
use Prooph\Snapshotter\Exception\RuntimeException;

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
     * @param SnapshotStore $snapshotStore
     * @param AggregateRepository[] $aggregateRepositories
     */
    public function __construct(SnapshotStore $snapshotStore, array $aggregateRepositories)
    {
        Assertion::notEmpty($aggregateRepositories);
        Assertion::allIsInstanceOf($aggregateRepositories, AggregateRepository::class);

        $this->snapshotStore = $snapshotStore;
        $this->aggregateRepositories = $aggregateRepositories;
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function __invoke(TakeSnapshot $command): void
    {
        $aggregateType = $command->aggregateType();

        if (! isset($this->aggregateRepositories[$aggregateType])) {
            throw new Exception\RuntimeException(sprintf(
                'No repository for aggregate type %s configured',
                $command->aggregateType()
            ));
        }

        $repository = $this->aggregateRepositories[$aggregateType];
        $aggregateRoot = $repository->getAggregateRoot($command->aggregateId());

        if (null === $aggregateRoot) {
            throw new RuntimeException(sprintf(
                'Could not find aggregate root %s with id %s',
                $aggregateType,
                $command->aggregateId()
            ));
        }

        $this->snapshotStore->save(new Snapshot(
            AggregateType::fromAggregateRootClass($aggregateType),
            $command->aggregateId(),
            $aggregateRoot,
            $repository->extractAggregateVersion($aggregateRoot),
            $command->createdAt()
        ));
    }
}
