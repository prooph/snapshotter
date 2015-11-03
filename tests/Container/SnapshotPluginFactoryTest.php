<?php
/*
 * This file is part of prooph/snapshotter.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/03/15 - 08:10 PM
 */

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
    public function it_creates_snapshot_plugin()
    {
        $commandBus = $this->prophesize(CommandBus::class);
        $container = $this->prophesize(ContainerInterface::class);

        $container->get('config')->willReturn([
            'prooph' => [
                'snapshotter' => [
                    'version_step' => 5,
                ]
            ]
        ]);
        $container->get(CommandBus::class)->willReturn($commandBus->reveal());

        $factory = new SnapshotPluginFactory();
        $this->assertInstanceOf(SnapshotPlugin::class, $factory($container->reveal()));
    }
}
