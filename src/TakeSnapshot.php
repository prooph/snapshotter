<?php

namespace Prooph\Snapshotter;

use Assert\Assertion;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;

/**
 * Class TakeSnapshot
 * @package Prooph\Snapshotter
 */
final class TakeSnapshot extends Command
{
    use PayloadTrait;

    /**
     * @param string $aggregateType
     * @param string $aggregateId
     * @param int $version
     * @return TakeSnapshot
     */
    public static function withData($aggregateType, $aggregateId, $version)
    {
        Assertion::string($aggregateType);
        Assertion::string($aggregateId);
        Assertion::min($version, 1);

        return new self([
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'version' => $version,
        ]);
    }

    /**
     * @return string
     */
    public function aggregateType()
    {
        return $this->payload['aggregate_type'];
    }

    /**
     * @return string
     */
    public function aggregateId()
    {
        return $this->payload['aggregate_id'];
    }

    /**
     * @return int
     */
    public function version()
    {
        return $this->payload['version'];
    }
}
