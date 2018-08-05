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

namespace Prooph\Snapshotter\Example;

require __DIR__ . '/../vendor/autoload.php';

use Amp\Loop;
use Amp\Promise;
use Generator;
use My\Model\UserWasCreated;
use My\Model\UserWasRenamed;
use Prooph\EventSourcing\Aggregate\AggregateRootTranslator;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\Aggregate\AsyncAggregateRepository;
use Prooph\EventSourcing\MessageTransformer;
use Prooph\EventStoreClient\CatchUpSubscriptionSettings;
use Prooph\EventStoreClient\ConnectionSettingsBuilder;
use Prooph\EventStoreClient\EventStoreConnectionBuilder;
use Prooph\EventStoreClient\Internal\EventStoreStreamCatchUpSubscription;
use Prooph\EventStoreClient\IpEndPoint;
use Prooph\EventStoreClient\ResolvedEvent;
use Prooph\EventStoreClient\SubscriptionDropReason;
use Prooph\EventStoreClient\UserCredentials;
use Prooph\SnapshotStore\InMemorySnapshotStore;
use Prooph\Snapshotter\Snapshotter;
use ProophTest\EventSourcing\Mock\User;
use Throwable;

// host and port of event store connection
$host = '127.0.0.1';
$port = 1113;

// credentials
$user = 'admin';
$password = 'changeit';

// let start with some settings
$settingsBuilder = new ConnectionSettingsBuilder();
$settingsBuilder->setDefaultUserCredentials(new UserCredentials($user, $password));

// this one is used for subscriptions
$connection = EventStoreConnectionBuilder::createAsyncFromIpEndPoint(
    new IpEndPoint($host, $port),
    $settingsBuilder->build()
);

$aggregateType = new AggregateType(['user' => User::class]);
$aggregateTranslator = new AggregateRootTranslator();

// not let's create the aggregate repository
// it doesn't matter if your app used a non-async repository, we use an async one here anyway
$aggregateRepository = new AsyncAggregateRepository(
    $connection,
    $aggregateType,
    $aggregateTranslator,
    new MessageTransformer([
        'user_was_created' => UserWasCreated::class,
        'user_was_renamed' => UserWasRenamed::class,
    ]),
    'user'
);

// now let's create the snapshotter
$snapshotter = new Snapshotter(
    $aggregateRepository,
    $aggregateTranslator,
    $aggregateType,
    new InMemorySnapshotStore() // we use in memory snapshot store for this demo purpose
);

// let's load last position
// this can also be stored in database
// (we don't want to create snapshots again from last script run)
if (\file_exists(__DIR__ . '/last_position') && \is_readable(__DIR__ . '/last_position')) {
    $lastPosition = (int) \file_get_contents(__DIR__ . '/last_position');
} else {
    $lastPosition = 0;
}

// once the script stops, we want to save last position as well
\register_shutdown_function(function () use ($snapshotter): void {
    \file_put_contents(__DIR__ . '/last_position', $snapshotter->lastEventNumber());
});

// nearly done, let's start the event loop and subscription
Loop::run(function () use ($connection, $snapshotter, $lastPosition): Generator {
    yield $connection->connectAsync();

    $subscription = $connection->subscribeToStreamFrom(
        '$ce-user',
        $lastPosition,
        CatchUpSubscriptionSettings::default(),
        function (EventStoreStreamCatchUpSubscription $subscription, ResolvedEvent $event) use ($snapshotter): Promise {
            // we remove "user-" from stream name, hence the 5
            $aggregateId = \substr($event->originalStreamName(), 5);
            $snapshotter->createSnapshotFor($aggregateId, $event->originalEventNumber());
        },
        function (EventStoreStreamCatchUpSubscription $subscription): void {
            echo 'we catched up all events, snapshots are live processed now' . PHP_EOL;
        },
        function (EventStoreStreamCatchUpSubscription $subscription, SubscriptionDropReason $reason, ?Throwable $exception): void {
            echo 'subscription dropped with reason: ' . $reason->name() . PHP_EOL;

            if ($exception) {
                echo 'exception: ' . $exception->getMessage();
                echo 'trace: ' . $exception->getTraceAsString();
            }
        }
    );

    yield $subscription->startAsync();
});
