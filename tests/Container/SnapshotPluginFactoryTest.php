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

namespace ProophTest\Snapshotter\Container;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Prooph\ServiceBus\CommandBus;
use Prooph\Snapshotter\Container\SnapshotPluginFactory;
use Prooph\Snapshotter\SnapshotPlugin;

/**
 * Class SnapshotPluginFactoryTest
 * @package ProophTest\Snapshotter\Container
 */
final class SnapshotPluginFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_snapshot_plugin(): void
    {
        $commandBus = $this->prophesize(CommandBus::class);
        $container = $this->prophesize(ContainerInterface::class);

        $container->get('config')->willReturn([
            'prooph' => [
                'snapshotter' => [
                    'version_step' => 5,
                    'event_names' => ['awesomeEvent'],
                ],
            ],
        ]);
        $container->get(CommandBus::class)->willReturn($commandBus->reveal());

        $factory = new SnapshotPluginFactory();
        $snapshotPlugin =  $factory($container->reveal());

        $this->assertInstanceOf(SnapshotPlugin::class, $snapshotPlugin);

        $reflectionClass = new \ReflectionClass($snapshotPlugin);
        $versionStepProperty = $reflectionClass->getProperty('versionStep');
        $versionStepProperty->setAccessible(true);
        $eventNamesProperty = $reflectionClass->getProperty('eventNames');
        $eventNamesProperty->setAccessible(true);

        $this->assertSame(5, $versionStepProperty->getValue($snapshotPlugin));
        $this->assertArrayHasKey(0, $eventNamesProperty->getValue($snapshotPlugin));
        $this->assertSame('awesomeEvent', $eventNamesProperty->getValue($snapshotPlugin)[0]);
    }
}
