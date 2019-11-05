# SSE

[![Latest version on Packagist][ico-version]][link-version]
[![Software License][ico-license]][link-license]
[![Build Status][ico-build]][link-build]

A Server-Sent Events server implementation in PHP.

For more information about SSE, see the [MDN documentation][link-mdn-web-docs].

## Installation
Open a command console, enter your project directory and execute:

```bash
composer require nijens/sse
```

## Usage
The SSE library functions with two main components:
1. An event publisher implementation (eg. the `DateTimeEventPublisher`): Providing the events to be sent
2. The `SseKernel`: Responsible for checking with the event publisher for new events and sending the events to the client (browser)

The following example shows how to initialize the `SseKernel` with an event publisher:
```php
<?php

require __DIR__.'/../vendor/autoload.php';

use Nijens\Sse\Event\DateTimeEventPublisher;
use Nijens\Sse\SseKernel;
use Symfony\Component\HttpFoundation\Request;

$eventPublisher = new DateTimeEventPublisher('date-time');

$kernel = new SseKernel($eventPublisher);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);

$response->send();
```

### Integrating the SseKernel inside a Symfony controller
When you're using the [Symfony Framework][link-symfony-framework] to create your application, you're still able to use the `SseKernel`
implementation inside a controller.

The following example shows a crude implementation of the `SseKernel` inside a controller:
```php
<?php

namespace App\Controller;

use Nijens\Sse\Event\DateTimeEventPublisher;
use Nijens\Sse\SseKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SseController
{
    public function __invoke(Request $request): StreamedResponse
    {
        $eventPublisher = new DateTimeEventPublisher('date-time');

        $kernel = new SseKernel($eventPublisher);

        return $kernel->handle($request);
    }
}
```
Optionally, you could use [Dependency Injection][link-symfony-dependency-injection] to change the kernel and
event publisher to services.

### Creating your own event publisher
This library provides the following event publishers:
* `DateTimeEventPublisher`: A working example, providing the current time as event
* `TransportEventPublisher`: A event publisher implementation for implementing a transport (eg. MySQL database implementation)

You're able to create your own event publisher implementation by implementing the `EventPublisherInterface` or
`ConnectedClientEventPublisherInterface`.

If you only want read the events from a database or other storage, it is recommended to create a `TransportInterface`
implementation for the `TransportEventPublisher`.

## Credits and acknowledgements

* Author: [Niels Nijens][link-author]

Also see the list of [contributors][link-contributors] who participated in this project.

## License
The SSE package is licensed under the MIT License. Please see the [LICENSE file][link-license] for details.

[ico-version]: https://img.shields.io/packagist/v/nijens/sse.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-build]: https://github.com/nijens/sse/workflows/Continuous%20Integration/badge.svg

[link-version]: https://packagist.org/packages/nijens/sse
[link-license]: LICENSE
[link-build]: https://github.com/nijens/sse/actions?workflow=Continuous+Integration
[link-mdn-web-docs]: https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events
[link-symfony-framework]: https://symfony.com
[link-symfony-dependency-injection]: https://symfony.com/doc/current/service_container.html
[link-author]: https://github.com/niels-nijens
[link-contributors]: https://github.com/nijens/sse/contributors
