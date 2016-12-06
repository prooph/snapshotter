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

namespace ProophTest\Snapshotter;

use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventSourcing\Snapshot\InMemorySnapshotStore;
use Prooph\EventStore\Projection\InMemoryEventStoreReadModelProjection;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Prooph\Snapshotter\SnapshotReadModel;
use Prooph\Snapshotter\StreamSnapshotProjection;
use ProophTest\EventSourcing\Mock\User;
use ProophTest\EventStore\TestCase;

class StreamSnapshotProjectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_takes_snapshots(): void
    {
        $user1 = User::nameNew('Aleks');
        $user1->changeName('Alex');

        $user2 = User::nameNew('Sasha');
        $user2->changeName('Sascha');

        $this->eventStore->create(
            new Stream(
                new StreamName('user'),
                new \ArrayIterator()
            )
        );

        $snapshotStore = new InMemorySnapshotStore();
        $aggregateType = AggregateType::fromAggregateRoot($user1);
        $aggregateRepository = new AggregateRepository(
            $this->eventStore,
            $aggregateType,
            new AggregateTranslator(),
            $snapshotStore,
            new StreamName('user'),
            false
        );

        $aggregateRepository->saveAggregateRoot($user1);
        $aggregateRepository->saveAggregateRoot($user2);

        $streamSnapshotProjection = new StreamSnapshotProjection(
            new InMemoryEventStoreReadModelProjection(
                $this->eventStore,
                'user-snapshots',
                new SnapshotReadModel(
                    $aggregateRepository,
                    new AggregateTranslator(),
                    $snapshotStore
                ),
                1000,
                5
            ),
            'user'
        );

        $streamSnapshotProjection(false);

        $this->assertEquals($user1, $snapshotStore->get($aggregateType, $user1->id())->aggregateRoot());
        $this->assertEquals($user2, $snapshotStore->get($aggregateType, $user2->id())->aggregateRoot());
    }
}
