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

namespace ProophTest\Snapshotter;

use PHPUnit\Framework\TestCase;
use Prooph\EventSourcing\Aggregate\AggregateTranslator;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\Aggregate\AsyncAggregateRepository;
use Prooph\SnapshotStore\SnapshotStore;
use Prooph\Snapshotter\Snapshotter;

class SnapshotterTest extends TestCase
{
    /**
     * @var Snapshotter
     */
    private $snapshotter;

    protected function setUp(): void
    {
        $this->snapshotter = new Snapshotter(
            $this->prophesize(AsyncAggregateRepository::class)->reveal(),
            $this->prophesize(AggregateTranslator::class)->reveal(),
            $this->prophesize(AggregateType::class)->reveal(),
            $this->prophesize(SnapshotStore::class)->reveal(),
            2
        );
    }

    /** @test */
    public function it_creates_snapshots_every_2_events(): void
    {
        $this->markTestIncomplete('TODO: IMPLEMENT');
    }
}
