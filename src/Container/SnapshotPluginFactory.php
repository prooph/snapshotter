<?php
/*
 * This file is part of prooph/snapshotter.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/03/15 - 05:59 PM
 */

namespace Prooph\Snapshotter\Container;

use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfig;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Container\ContainerInterface;
use Prooph\ServiceBus\CommandBus;
use Prooph\Snapshotter\SnapshotPlugin;

/**
 * Class SnapshotPluginFactory
 * @package Prooph\Snapshotter\Container
 */
final class SnapshotPluginFactory implements RequiresConfig, RequiresMandatoryOptions, ProvidesDefaultOptions
{
    use ConfigurationTrait;

    /**
     * @param ContainerInterface $container
     * @return SnapshotPlugin
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $config = $this->options($config);

        return new SnapshotPlugin($container->get(CommandBus::class), $config['version_step']);
    }

    /**
     * @return string
     */
    public function vendorName()
    {
        return 'prooph';
    }

    /**
     * @return string
     */
    public function packageName()
    {
        return 'snapshotter';
    }

    /**
     * @return array
     */
    public function defaultOptions()
    {
        return [
            'version_step' => 5
        ];
    }

    /**
     * @return array
     */
    public function mandatoryOptions()
    {
        return [
            'version_step'
        ];
    }
}
