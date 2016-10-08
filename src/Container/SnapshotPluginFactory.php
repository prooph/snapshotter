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

namespace Prooph\Snapshotter\Container;

use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfig;
use Interop\Config\RequiresConfigId;
use Interop\Container\ContainerInterface;
use Prooph\ServiceBus\CommandBus;
use Prooph\Snapshotter\Exception\InvalidArgumentException;
use Prooph\Snapshotter\SnapshotPlugin;

/**
 * Class SnapshotPluginFactory
 * @package Prooph\Snapshotter\Container
 */
final class SnapshotPluginFactory implements RequiresConfig, RequiresConfigId, ProvidesDefaultOptions
{
    use ConfigurationTrait;

    /**
     * @var string
     */
    private $configId;

    /**
     * Creates a new instance from a specified config, specifically meant to be used as static factory.
     *
     * In case you want to use another config key than provided by the factories, you can add the following factory to
     * your config:
     *
     * <code>
     * <?php
     * return [
     *     'prooph.snapshotter.service_name' => [SnapshotPluginFactory::class, 'service_name'],
     * ];
     * </code>
     *
     * @throws InvalidArgumentException
     */
    public static function __callStatic(string $name, array $arguments): SnapshotPlugin
    {
        if (! isset($arguments[0]) || ! $arguments[0] instanceof ContainerInterface) {
            throw new InvalidArgumentException(
                sprintf('The first argument must be of type %s', ContainerInterface::class)
            );
        }
        return (new static($name))->__invoke($arguments[0]);
    }

    public function __construct(string $configId = 'default')
    {
        $this->configId = $configId;
    }

    public function __invoke(ContainerInterface $container): SnapshotPlugin
    {
        $config = $container->get('config');
        $config = $this->options($config, $this->configId);

        return new SnapshotPlugin($container->get($config['command_bus']), $config['version_step']);
    }

    public function dimensions(): array
    {
        return ['prooph', 'snapshotter'];
    }

    public function defaultOptions(): array
    {
        return [
            'version_step' => 5,
            'command_bus' => CommandBus::class,
        ];
    }
}
