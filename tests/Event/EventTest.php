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

use Nijens\Sse\Event\Event;
use PHPUnit\Framework\TestCase;

/**
 * EventTest.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class EventTest extends TestCase
{
    /**
     * Tests if Event::__toString returns the expected string based on the event stream format.
     *
     * @dataProvider provideToStringTestCases
     *
     * @param string      $id
     * @param string      $data
     * @param string|null $name
     * @param string      $expectedString
     */
    public function testToString(string $id, string $data, ?string $name, string $expectedString)
    {
        $event = new Event($id, $data, $name);

        $this->assertEquals($expectedString, strval($event));
    }

    /**
     * Returns the test cases for @see testToString.
     *
     * @return array
     */
    public function provideToStringTestCases(): array
    {
        return array(
            array(
                '1',
                "event\nwith\nmultiple\nlines.",
                'foo',
                "id: 1\nevent: foo\ndata: event\ndata: with\ndata: multiple\ndata: lines.\n\n",
            ),
            array(
                '2',
                'Foo.',
                null,
                "id: 2\ndata: Foo.\n\n",
            ),
        );
    }
}
