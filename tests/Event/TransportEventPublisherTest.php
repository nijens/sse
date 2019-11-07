<?php

namespace Nijens\Sse\Tests\Event;

use Nijens\Sse\Event\Event;
use Nijens\Sse\Event\TransportEventPublisher;
use Nijens\Sse\Transport\TransportInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * Tests the @see TransportEventPublisher.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class TransportEventPublisherTest extends TestCase
{
    /**
     * @var TransportEventPublisher
     */
    private $eventPublisher;

    /**
     * @var MockObject
     */
    private $transportMock;

    /**
     * Creates a new TransportEventPublisher instance for testing.
     */
    protected function setUp(): void
    {
        $this->transportMock = $this->getMockBuilder(TransportInterface::class)
            ->getMock();

        $this->eventPublisher = new TransportEventPublisher($this->transportMock);
    }

    /**
     * Tests if {@see TransportEventPublisher::__invoke} with new events:
     * 1. Registers the connection with the transport.
     * 2. Retrieves the new events from the transport.
     * 3. Updates the connection with the last event ID.
     * 4. Returns the new events.
     */
    public function testInvokeWithNewEvents(): void
    {
        $event = new Event('1', 'data', 'test-event');

        $this->transportMock->expects($this->once())
            ->method('registerConnection')
            ->with($this->isInstanceOf(UuidInterface::class), null);

        $this->transportMock->expects($this->once())
            ->method('getNewEvents')
            ->with(null)
            ->willReturn(array($event));

        $this->transportMock->expects($this->once())
            ->method('updateConnection')
            ->with($this->isInstanceOf(UuidInterface::class), '1');

        $result = ($this->eventPublisher)(null);

        $this->assertSame(array($event), $result);
    }

    /**
     * Tests if {@see TransportEventPublisher::__invoke} with last event ID and without new events in the transport:
     * 1. Registers the connection with the transport with the specified last event ID.
     * 2. Retrieves the new events from the transport.
     * 3. Updates the connection with the specified last event ID.
     * 4. Returns an empty array.
     */
    public function testInvokeWithoutNewEvents(): void
    {
        $this->transportMock->expects($this->once())
            ->method('registerConnection')
            ->with($this->isInstanceOf(UuidInterface::class), '1');

        $this->transportMock->expects($this->once())
            ->method('getNewEvents')
            ->with('1')
            ->willReturn(array());

        $this->transportMock->expects($this->once())
            ->method('updateConnection')
            ->with($this->isInstanceOf(UuidInterface::class), '1');

        $result = ($this->eventPublisher)('1');

        $this->assertSame(array(), $result);
    }

    /**
     * Tests if {@see TransportEventPublisher::disconnectClient} does not call the
     * {@see TransportInterface::unregisterConnection} when there is registered connection
     * (no connection ID available).
     */
    public function testDisconnectClientWithoutRegisteredConnection(): void
    {
        $this->transportMock->expects($this->never())
            ->method('unregisterConnection')
            ->with($this->isInstanceOf(UuidInterface::class));

        $this->eventPublisher->disconnectClient();
    }

    /**
     * Tests if {@see TransportEventPublisher::disconnectClient} calls the
     * {@see TransportInterface::unregisterConnection} when there is a registered connection.
     */
    public function testDisconnectClient(): void
    {
        ($this->eventPublisher)(null);

        $this->transportMock->expects($this->once())
            ->method('unregisterConnection')
            ->with($this->isInstanceOf(UuidInterface::class));

        $this->eventPublisher->disconnectClient();
    }
}
