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

namespace Prooph\Snapshotter;

use Prooph\Common\Messaging\Message;
use Prooph\EventStore\Projection\ReadModelProjection;

class StreamSnapshotProjection
{
    /**
     * @var ReadModelProjection
     */
    private $readModelProjection;

    /**
     * @var string
     */
    private $streamName;

    public function __construct(ReadModelProjection $readModelProjection, string $streamName)
    {
        $this->readModelProjection = $readModelProjection;
        $this->streamName = $streamName;
    }

    public function __invoke(bool $keepRunning = true)
    {
        $this->readModelProjection
            ->fromStream($this->streamName)
            ->whenAny(function ($state, Message $event): void {
                $this->readModel()->stack('replay', $event);
            })
            ->run($keepRunning);
    }
}
