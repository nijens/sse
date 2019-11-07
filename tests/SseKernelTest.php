<?php
/**
 * This file is part of the sse package.
 *
 * (c) Niels Nijens <nijens.niels@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nijens\Sse\Tests;

use Nijens\Sse\Event\Event;
use Nijens\Sse\Event\EventPublisherInterface;
use Nijens\Sse\SseKernel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Tests the @see SseKernel.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class SseKernelTest extends TestCase
{
    /**
     * @var SseKernel
     */
    private $kernel;

    /**
     * @var MockObject
     */
    private $eventPublisherMock;

    /**
     * Register the ClockMock for time() sensitive tests.
     */
    public static function setUpBeforeClass(): void
    {
        ClockMock::register(SseKernel::class);
    }

    /**
     * Creates a new SseKernel instance for testing.
     */
    protected function setUp(): void
    {
        $this->eventPublisherMock = $this->getMockBuilder(EventPublisherInterface::class)
            ->getMock();

        $this->kernel = new SseKernel($this->eventPublisherMock, 30, 1);
    }

    /**
     * Disable the ClockMock.
     */
    protected function tearDown(): void
    {
        ClockMock::withClockMock(false);
    }

    /**
     * Tests if SseKernel::handle returns the expected StreamedResponse instance.
     */
    public function testHandle()
    {
        $request = Request::create('/');

        $response = $this->kernel->handle($request);

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame(StreamedResponse::HTTP_OK, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'text/event-stream'));
        $this->assertTrue($response->headers->contains('Cache-Control', 'no-cache, private'));
        $this->assertTrue($response->headers->contains('Connection', 'keep-alive'));
        $this->assertTrue($response->headers->contains('X-Accel-Buffering', 'no'));
    }

    /**
     * Tests if calling StreamedResponse::sendContent starts retrieving events from the event publisher and
     * sending them to the client.
     */
    public function testHandleSendContentWithEvents()
    {
        ClockMock::withClockMock(1564584138);

        $event = new Event('event-id', 'event-data');

        $this->eventPublisherMock->expects($this->exactly(2))
            ->method('__invoke')
            ->withConsecutive(
                array(null),
                array('event-id')
            )
            ->willReturnOnConsecutiveCalls(
                array($event),
                null
            );

        $request = Request::create('/');

        $response = $this->kernel->handle($request);

        ob_start();
        ob_start(); // Start a second output buffer to catch the ob_flush() call.
        $response->sendContent();
        ob_end_flush();
        $responseOutput = ob_get_clean();

        $this->assertSame(": 1564584138\n\nid: event-id\ndata: event-data\n\n", $responseOutput);
    }

    /**
     * Tests if calling StreamedResponse::sendContent sends a connection keep-alive comment to the client.
     */
    public function testHandleSendContentWithKeepAlive()
    {
        ClockMock::withClockMock(1564584138);

        $this->eventPublisherMock->expects($this->exactly(5))
            ->method('__invoke')
            ->with(null)
            ->willReturnOnConsecutiveCalls(
                array(),
                array(),
                array(),
                array(),
                null
            );

        $request = Request::create('/');

        $this->kernel = new SseKernel($this->eventPublisherMock, 2, 1);
        $response = $this->kernel->handle($request);

        ob_start();
        ob_start(); // Start a second output buffer to catch the ob_flush() call.
        $response->sendContent();
        ob_end_flush();
        $responseOutput = ob_get_clean();

        $this->assertSame(": 1564584138\n\n: 1564584140\n\n: 1564584142\n\n", $responseOutput);
    }
}
