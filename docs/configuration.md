# Configuration
 
To enable the Snapshot-Plugin simply attach it as plugin to the event-store. 
It needs a list of aggregate root => aggregate repositories and a version step.
Version step 5 f.e. means take a snapshot every 5 versions.
If you use the provided factories from event-store and the snapshotter, you can simply do this by configuration:

    return [
        'prooph' => [
            'event_store' => [
                'plugins' => [
                    \Prooph\Snapshotter\SnapshotPlugin::class,
                ]
            ],
            'snapshotter' => [
                'version_step' => 5,
                'aggregate_repositories' => [
                    'My\Domain\AggregateRoot' => \My\Domain\AggregateRootRepository::class,
                ]
            ],
            'command_bus' => [
                'router' => [
                    'routes' => [
                        \Prooph\Snapshotter\TakeSnapshot::class => \Prooph\Snapshotter\Snapshotter::class,
                    ]
                ]   
            ]
        ]
    ];

If you need to wire this manually, take a look at the provided factories, this is also very easy to achieve.

For asynchronous snapshots you'll need to route the `Prooph\Snapshotter\TakeSnapshot` to the corresponding
message producer and write a consumer that routes incoming messages to the `Prooph\Snapshotter\Snapshotter`.

Also our example app [proophessor-do](https://github.com/prooph/proophessor-do) includes a snapshotting tutorial with
a step by step guide.
