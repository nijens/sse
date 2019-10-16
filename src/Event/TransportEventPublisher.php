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

use Nijens\Sse\Transport\TransportInterface;
use Ramsey\Uuid\Uuid;

/**
 * Handles registering a new connection and reading new events from a transport.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class TransportEventPublisher implements EventPublisherInterface
{
    /**
     * @var TransportInterface
     */
    private $transport;

    /**
     * @var string
     */
    private $connectionId;

    /**
     * Constructs a new TransportEventPublisher instance.
     *
     * @param TransportInterface $transport
     */
    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(?string $lastEventId): array
    {
        if (isset($this->connectionId) === false) {
            $this->connectionId = Uuid::uuid4();
            $this->transport->registerConnection($this->connectionId, $lastEventId);
        }

        $events = $this->transport->getNewEvents($lastEventId);
        $lastEvent = end($events);
        if ($lastEvent instanceof EventInterface) {
            $lastEventId = $lastEvent->getId();
        }

        $this->transport->updateConnection($this->connectionId, $lastEventId);

        reset($events);

        return $events;
    }
}
