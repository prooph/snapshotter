<?php
/**
 * This file is part of the prooph/snapshotter.
 * (c) 2015-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @interitdoc
     */
    public function dimensions()
    {
        return ['prooph', 'snapshotter'];
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
