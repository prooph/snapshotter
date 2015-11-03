<?php
/*
 * This file is part of prooph/snapshotter.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/02/15 - 03:35 PM
 */

namespace ProophTest\Snapshotter;

use PHPUnit_Framework_TestCase as TestCase;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\Adapter\InMemoryAdapter;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\Aggregate\ConfigurableAggregateTranslator;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream\Stream;
use Prooph\EventStore\Stream\StreamName;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Prooph\Snapshotter\SnapshotPlugin;
use Prooph\Snapshotter\TakeSnapshot;
use ProophTest\EventStore\Mock\User;
use ProophTest\EventStore\Mock\UserCreated;
use ProophTest\EventStore\Mock\UsernameChanged;

/**
 * Class SnapshotPluginTest
 * @package ProophTest\Snapshotter
 */
final class SnapshotPluginTest extends TestCase
{
    /**
     * @var EventStore
     */
    protected $eventStore;

    protected function setUp()
    {
        $inMemoryAdapter = new InMemoryAdapter();
        $eventEmitter    = new ProophActionEventEmitter();

        $this->eventStore = new EventStore($inMemoryAdapter, $eventEmitter);
    }

    /**
     * @test
     */
    public function it_publishes_take_snapshot_commands_for_all_known_aggregates()
    {
        $repository = new AggregateRepository(
            $this->eventStore,
            AggregateType::fromAggregateRootClass('ProophTest\EventStore\Mock\User'),
            new ConfigurableAggregateTranslator()
        );

        $result = [];

        $router = new CommandRouter();
        $router->route(TakeSnapshot::class)->to(function(TakeSnapshot $command) use (&$result) {
            $result[] = [
                'aggregate_type' => $command->aggregateType(),
                'aggregate_id' => $command->aggregateId()
            ];
        });

        $commandBus = new CommandBus();
        $commandBus->utilize($router);

        $plugin = new SnapshotPlugin($commandBus, 2);
        $plugin->setUp($this->eventStore);

        $this->eventStore->beginTransaction();

        $this->eventStore->create(new Stream(new StreamName('event_stream'), new \ArrayIterator()));

        $this->eventStore->commit();

        $this->eventStore->beginTransaction();

        $user = User::create('Alex', 'contact@prooph.de');

        $repository->addAggregateRoot($user);

        $this->eventStore->commit();

        $this->eventStore->beginTransaction();

        $user = $repository->getAggregateRoot($user->getId()->toString());

        $user->changeName('John');
        $user->changeName('Jane');
        $user->changeName('Jim');

        $this->eventStore->commit();

        $this->eventStore->beginTransaction();

        $eventWithoutMetadata1 = UsernameChanged::with(
            ['new_name' => 'John Doe'],
            5
        );

        $eventWithoutMetadata2 = UsernameChanged::with(
            ['new_name' => 'Jane Doe'],
            6
        );

        $this->eventStore->appendTo(new StreamName('event_stream'), new \ArrayIterator([
            $eventWithoutMetadata1,
            $eventWithoutMetadata2
        ]));

        $this->eventStore->commit();

        $this->assertCount(2, $result);
        $this->assertArrayHasKey('aggregate_type', $result[0]);
        $this->assertArrayHasKey('aggregate_id', $result[0]);
        $this->assertArrayHasKey('aggregate_type', $result[1]);
        $this->assertArrayHasKey('aggregate_id', $result[1]);
        $this->assertEquals(User::class, $result[0]['aggregate_type']);
        $this->assertEquals(User::class, $result[1]['aggregate_type']);
    }
}
