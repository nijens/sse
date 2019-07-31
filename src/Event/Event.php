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

/**
 * The Server Sent Event value-object.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class Event implements EventInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string
     */
    private $data;

    /**
     * Constructs a new Event instance.
     *
     * @param string      $id
     * @param string      $data
     * @param string|null $name
     */
    public function __construct(string $id, string $data, string $name = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $eventString = sprintf("id: %s\n", $this->id);
        if (isset($this->name)) {
            $eventString .= sprintf("event: %s\n", $this->name);
        }
        $eventString .= sprintf(
            "data: %s\n\n",
            implode(
                "\ndata: ",
                explode("\n", $this->data)
            )
        );

        return $eventString;
    }
}
