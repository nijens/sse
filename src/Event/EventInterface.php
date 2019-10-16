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
 * The Server Sent Event value-object specification.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
interface EventInterface
{
    /**
     * Returns the ID of this event.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Returns the event in the event stream format.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events#Event_stream_format
     *
     * @return string
     */
    public function __toString(): string;
}
