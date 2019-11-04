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

/**
 * Mocks the {@see connection_aborted} function.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class ConnectionMock
{
    /**
     * @var bool|null
     */
    private static $aborted;

    /**
     * Activates the connection_aborted() value mocking. Provide null to deactivate mocking.
     *
     * @param bool|null $aborted
     */
    public static function withConnectionAborted(?bool $aborted): void
    {
        self::$aborted = $aborted;
    }

    /**
     * Handles returning (mocked) connection_aborted() values.
     *
     * @return int
     */
    public static function connection_aborted(): int
    {
        if (static::$aborted === null) {
            return \connection_aborted();
        }

        return (int) static::$aborted;
    }

    /**
     * Registers a connection_aborted() function inside the namespace of the specified class for mocking.
     *
     * @param string $class
     */
    public static function register(string $class): void
    {
        $self = get_called_class();
        $namespace = substr($class, 0, strrpos($class, '\\'));

        eval(<<<EOPHP
namespace $namespace;

function connection_aborted()
{
    return \\$self::connection_aborted();
}
EOPHP
        );
    }
}
