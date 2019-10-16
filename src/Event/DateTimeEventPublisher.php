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

use DateTime;
use Ramsey\Uuid\Uuid;

/**
 * Publishes the current date and time to the Server Sent Event stream.
 * This class is mostly meant as a working example of an event publisher.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class DateTimeEventPublisher implements EventPublisherInterface
{
    /**
     * @var string|null
     */
    private $eventName;

    /**
     * Constructs a new DateTimeEventPublisher instance.
     *
     * @param string|null $eventName
     */
    public function __construct(string $eventName = null)
    {
        $this->eventName = $eventName;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(?string $lastEventId): array
    {
        return array(
            new Event(
                Uuid::uuid1(),
                (new DateTime())->format(DateTime::RFC3339),
                $this->eventName
            ),
        );
    }
}
