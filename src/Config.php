<?php

namespace HughCube\Profiler;

use ArrayAccess;

class Config implements ArrayAccess
{
    /**
     * @var array<string, mixed>
     */
    protected $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param  string  $keys
     * @return bool
     */
    public function has(...$keys): bool
    {
        if (empty($keys)) {
            return false;
        }

        if (empty($this->items)) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $this->items;
            if (array_key_exists($key, $subKeyArray)) {
                continue;
            }

            foreach (explode('.', (string) $key) as $segment) {
                if (array_key_exists($segment, $subKeyArray)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param  null|integer|string  $key
     */
    public function get($key, $default = null)
    {
        if (null === $key) {
            return $this->items;
        }

        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        $subKeyArray = $this->items;
        foreach (explode('.', (string) $key) as $segment) {
            if (array_key_exists($segment, $subKeyArray)) {
                $subKeyArray = $subKeyArray[$segment];
            } else {
                return $default;
            }
        }
        return $subKeyArray;
    }

    /**
     * @param  null|integer|string  $key
     * @return static
     */
    public function set($key, $value = null): Config
    {
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->set($offset, null);
    }
}
