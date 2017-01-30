<?php
/**
 * This file is part of the prooph/snapshotter.
 * (c) 2015-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\Snapshotter;

use Prooph\Common\Messaging\Message;
use Prooph\EventStore\Projection\ReadModelProjection;

class CategorySnapshotProjection
{
    /**
     * @var ReadModelProjection
     */
    private $readModelProjection;

    /**
     * @var string
     */
    private $category;

    public function __construct(ReadModelProjection $readModelProjection, string $category)
    {
        $this->readModelProjection = $readModelProjection;
        $this->category = $category;
    }

    public function __invoke(bool $keepRunning = true)
    {
        $this->readModelProjection
            ->fromCategory($this->category)
            ->whenAny(function ($state, Message $event): void {
                $this->readModel()->stack('replay', $event);
            })
            ->run($keepRunning);
    }
}
