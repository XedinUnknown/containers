<?php

namespace Dhii\Container;

use Dhii\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use stdClass;

/**
 * A container implementation that provides access to hierarchical data in the form a container tree.
 *
 * This implementation dynamically transforms hierarchical data into a tree of containers, on-demand. The transformation
 * is performed "in-place", converting internal array and object values into containers without producing a copy or
 * internal cache, making this implementation very memory-friendly.
 *
 * **Example usage:**
 *
 * ```php
 * $data = [
 *      'config' => [
 *          'db' => [
 *              'host' => 'localhost',
 *              'port' => 3306,
 *          ],
 *      ]
 * ];
 *
 * $container = new HierarchicalContainer($data);
 * $container->get('config')->get('db')->get('host'); // "localhost"
 * ```
 *
 * @since [*next-version*]
 * @see   PathContainer For an implementation that compliments this one by allowing container trees to be accessed using
 *                      path-like keys.
 * @see   SegmentingContainer For an implementation that achieves a similar effect but for flat hierarchies.
 */
class HierarchyContainer implements ContainerInterface
{
    /**
     * @since [*next-version*]
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array $data The hierarchical data for which to create the container tree.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->data)) {
            throw new NotFoundException("Key '{$key}' does not exist", 0, null, $this);
        }

        $value = $this->data[$key];

        if (is_array($value) || $value instanceof stdClass) {
            $value = $this->data[$key] = new HierarchyContainer($value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     *
     * @since [*next-version*]
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }
}
