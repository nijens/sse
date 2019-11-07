<?php
/**
 * This file is part of the sse package.
 *
 * (c) Niels Nijens <nijens.niels@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nijens\Sse\Transport;

use Nijens\Sse\Event\EventInterface;
use Nijens\Sse\Event\TransportEventPublisher;
use Ramsey\Uuid\UuidInterface;

/**
 * Interface definition of a transport for the @see TransportEventPublisher.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
interface TransportInterface
{
    /**
     * Registers a new client connection with the data transport.
     *
     * @param UuidInterface $connectionId
     * @param string|null   $lastEventId
     */
    public function registerConnection(UuidInterface $connectionId, ?string $lastEventId): void;

    /**
     * Update a client connection from the data transport.
     *
     * @param UuidInterface $connectionId
     * @param string|null   $lastEventId
     */
    public function updateConnection(UuidInterface $connectionId, ?string $lastEventId): void;

    /**
     * Unregisters a client connection from the data transport.
     *
     * @param UuidInterface $connectionId
     */
    public function unregisterConnection(UuidInterface $connectionId): void;

    /**
     * @param string|null $lastEventId
     *
     * @return EventInterface[]
     */
    public function getNewEvents(?string $lastEventId = null): array;
}
