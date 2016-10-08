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

namespace Prooph\Snapshotter\Exception;

/**
 * Class InvalidArgumentException
 * @package Prooph\Snapshotter\Exception
 */
final class InvalidArgumentException extends \InvalidArgumentException implements SnapshotterException
{
}
