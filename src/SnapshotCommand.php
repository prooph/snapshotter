<?php
/*
 * This file is part of prooph/snapshotter.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11/02/15 - 03:52 PM
 */

namespace Prooph\Snapshotter;

use Prooph\Common\Messaging\Message;

interface SnapshotCommand extends Message
{
    /**
     * @return string
     */
    public function aggregateType();

    /**
     * @return string
     */
    public function aggregateId();
}
