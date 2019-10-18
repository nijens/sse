<?php
/**
 * This file is part of the sse package.
 *
 * (c) Niels Nijens <nijens.niels@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nijens\Sse;

use Nijens\Sse\Event\EventPublisherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Manages retrieving events from an event publisher and sending them to the client.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class SseKernel
{
    /**
     * @var EventPublisherInterface
     */
    private $eventPublisher;

    /**
     * @var int
     */
    private $eventPublisherDelay;

    /**
     * @var int
     */
    private $keepAliveTime;

    /**
     * @var string|null
     */
    private $lastEventId;

    /**
     * Constructs a new SseKernel instance.
     *
     * @param EventPublisherInterface $eventPublisher
     * @param int                     $keepAliveTime       The keep-alive time in seconds
     * @param int                     $eventPublisherDelay The delay time between event publisher calls in seconds
     */
    public function __construct(
        EventPublisherInterface $eventPublisher,
        int $keepAliveTime = 300,
        int $eventPublisherDelay = 1
    ) {
        $this->eventPublisher = $eventPublisher;
        $this->eventPublisherDelay = $eventPublisherDelay;

        if ($keepAliveTime < 1) {
            $keepAliveTime = 1;
        }

        $this->keepAliveTime = $keepAliveTime;
    }

    /**
     * @param Request $request
     *
     * @return StreamedResponse
     */
    public function handle(Request $request): StreamedResponse
    {
        $this->initialize($request);

        $callback = function () {
            $this->processEvents();
        };

        $response = new StreamedResponse(
            $callback,
            StreamedResponse::HTTP_OK,
            array(
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no', // Disables FastCGI Buffering on Nginx.
            )
        );

        return $response;
    }

    /**
     * @param Request $request
     */
    private function initialize(Request $request): void
    {
        set_time_limit(0);

        $this->lastEventId = $request->headers->get('Last-Event-ID');
    }

    /**
     * Handles sending of the event data during the duration of the request/process.
     */
    private function processEvents(): void
    {
        $startTime = time();
        while (true) {
            if (connection_aborted() === 1) {
                return;
            }

            if ($startTime % $this->keepAliveTime === 0) {
                // Send a comment to keep the connection alive.
                $this->send(sprintf(": %s\n\n", time()));
            }

            $events = ($this->eventPublisher)($this->lastEventId);
            if ($events === null) {
                break;
            }

            foreach ($events as $event) {
                $this->send($event->__toString());

                $this->lastEventId = $event->getId();
            }

            $this->sleep();
        }
    }

    /**
     * Sends data to the event stream and flush the output buffer.
     *
     * @param string $data
     */
    private function send(string $data): void
    {
        echo $data;
        flush();
    }

    /**
     * Delay sending data to the event stream.
     */
    private function sleep(): void
    {
        sleep($this->eventPublisherDelay);
    }
}
