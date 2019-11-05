<?php
/**
 * This file is part of the sse package.
 *
 * (c) Niels Nijens <nijens.niels@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nijens\Sse\Event;

/**
 * Returns the events for publication to the Server-Sent event stream.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
interface EventPublisherInterface
{
    /**
     * Returns the events for publication. When null is returned the event stream should stop.
     *
     * @return Event[]|null
     */
    public function __invoke(?string $lastEventId): ?array;
}
