<?php
/*
 * This file is part of prooph/snapshotter.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/02/15 - 04:37 PM
 */

namespace ProophTest\Snapshotter;

use PHPUnit_Framework_TestCase as TestCase;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Snapshot\SnapshotStore;
use Prooph\Snapshotter\Snapshotter;
use Prooph\Snapshotter\TakeSnapshot;
use ProophTest\EventStore\Mock\User;

/**
 * Class SnapshotterTest
 * @package ProophTest\Snapshotter
 */
final class SnapshotterTest extends TestCase
{
    /**
     * @test
     */
    public function it_takes_snapshots()
    {
        $user = User::create('Alex', 'contact@prooph.de');

        $repository = $this->prophesize(AggregateRepository::class);
        $repository->getAggregateRoot('some id')->willReturn($user);
        $repository->extractAggregateVersion($user)->willReturn(1);

        $snapshotStore = $this->prophesize(SnapshotStore::class);
        $snapshotStore->save($this->any());

        $snapshotter = new Snapshotter($snapshotStore->reveal(), [
            'ProophTest\EventStore\Mock\User' => $repository->reveal()
        ]);

        $snapshotter(TakeSnapshot::withData('ProophTest\EventStore\Mock\User', 'some id'));
    }

    /**
     * @test
     * @expectedException \Assert\InvalidArgumentException
     */
    public function it_throws_exception_when_no_repository_given()
    {
        $snapshotStore = $this->prophesize(SnapshotStore::class);

        new Snapshotter($snapshotStore->reveal(), []);
    }

    /**
     * @test
     * @expectedException \Prooph\Snapshotter\Exception\RuntimeException
     * @expectedExceptionMessage No repository for aggregate type ProophTest\EventStore\Mock\Todo configured
     */
    public function it_throws_exception_when_aggregate_root_cannot_get_handled()
    {
        $repository = $this->prophesize(AggregateRepository::class);

        $snapshotStore = $this->prophesize(SnapshotStore::class);

        $snapshotter = new Snapshotter($snapshotStore->reveal(), [
            'ProophTest\EventStore\Mock\User' => $repository->reveal()
        ]);

        $snapshotter(TakeSnapshot::withData('ProophTest\EventStore\Mock\Todo', 'some id'));
    }

    /**
     * @test
     * @expectedException \Prooph\Snapshotter\Exception\RuntimeException
     * @expectedExceptionMessage Could not find aggregate root
     */
    public function it_throws_exception_when_aggregate_root_not_found()
    {
        $repository = $this->prophesize(AggregateRepository::class);

        $snapshotStore = $this->prophesize(SnapshotStore::class);

        $snapshotter = new Snapshotter($snapshotStore->reveal(), [
            'ProophTest\EventStore\Mock\User' => $repository->reveal()
        ]);

        $snapshotter(TakeSnapshot::withData('ProophTest\EventStore\Mock\User', 'invalid id'));
    }
}
