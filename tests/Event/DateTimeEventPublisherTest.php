<?php
/**
 * This file is part of the sse package.
 *
 * (c) Niels Nijens <nijens.niels@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nijens\Sse\Tests\Event;

use Nijens\Sse\Event\DateTimeEventPublisher;
use Nijens\Sse\Event\Event;
use PHPUnit\Framework\TestCase;

/**
 * Tests the @see DateTimeEventPublisher.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class DateTimeEventPublisherTest extends TestCase
{
    /**
     * @var DateTimeEventPublisher
     */
    private $eventPublisher;

    /**
     * Creates a new DateTimeEventPublisher instance for testing.
     */
    protected function setUp(): void
    {
        $this->eventPublisher = new DateTimeEventPublisher('date-time');
    }

    /**
     * Tests if DateTimeEventPublisher::__invoke returns the expected result.
     */
    public function testInvoke()
    {
        $events = $this->eventPublisher->__invoke(null);

        $this->assertIsIterable($events);
        $this->assertCount(1, $events);
        $this->assertInstanceOf(Event::class, $events[0]);
    }
}
