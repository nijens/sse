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
 * Returns the events for publication to the Server-Sent event stream and handles disconnection of a connected client.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
interface ConnectedClientEventPublisherInterface extends EventPublisherInterface
{
    /**
     * Disconnects the previously connected client.
     */
    public function disconnectClient(): void;
}
