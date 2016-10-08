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
use Interop\Config\RequiresConfig;
use Interop\Config\RequiresMandatoryOptions;
use Interop\Container\ContainerInterface;
use Prooph\EventStore\Snapshot\SnapshotStore;
use Prooph\Snapshotter\Snapshotter;

/**
 * Class SnapshotterFactory
 * @package Prooph\Snapshotter\Container
 */
final class SnapshotterFactory implements RequiresConfig, RequiresMandatoryOptions
{
    use ConfigurationTrait;

    /**
     * @param ContainerInterface $container
     * @return Snapshotter
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $config = $this->options($config);

        $aggregateRepositories = [];

        foreach ($config['aggregate_repositories'] as $aggregateType => $aggregateRepositoryClass) {
            $aggregateRepositories[$aggregateType] = $container->get($aggregateRepositoryClass);
        }

        return new Snapshotter($container->get(SnapshotStore::class), $aggregateRepositories);
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
    public function mandatoryOptions()
    {
        return [
            'aggregate_repositories'
        ];
    }
}
