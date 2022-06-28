<?php

declare(strict_types=1);

namespace ZeleznyPa\ArrayWrapper;

use ArrayAccess;
use BadMethodCallException;
use Countable;
use InvalidArgumentException;

use function array_key_exists;
use function count;
use function func_num_args;
use function lcfirst;
use function preg_match;
use function sprintf;

/**
 * @template TInnerArray of array
 * @implements ArrayAccess<key-of<TInnerArray>, value-of<TInnerArray>>
 * @suppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ArrayWrapper implements ArrayAccess, Countable
{
    /** @var TInnerArray */
    protected $array;

    /**
     * @param TInnerArray $array
     */
    public function __construct(array $array)
    {
        $this->setArray($array);
    }

    /**
     * @template UInnerArray of array
     * @param UInnerArray $array [OPTIONAL]
     * @return self<UInnerArray>
     */
    public static function create(array $array = [])
    {
        $arrayWrapper = new self($array);
        return $arrayWrapper;
    }

    /**
     * Helper that allows to access array properties by the magic getters and setter
     *
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $result = preg_match('~^(?P<method>(?:get|has|is|set|unset))(?P<offset>.*)$~', $name, $match);
        if ($result !== 1) {
            throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', __CLASS__, $name));
        }
        $method = isset($match['method']) ? $match['method'] : '';
        $offset = lcfirst(isset($match['offset']) ? $match['offset'] : '');
        $return = null;
        if ($method === 'get') {
            $return = $this->offsetGet($offset);
        } elseif (($method === 'has') || ($method === 'is')) {
            $return = $this->offsetExists($offset);
        } elseif ($method === 'set') {
            $this->offsetSet($offset, $arguments[0]);
        } elseif ($method === 'unset') {
            $this->offsetUnset($offset);
        }
        return $return;
    }

    /**
     * Array property generic getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->array[$name];
    }

    /**
     * Array property generic checker
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->array);
    }

    /**
     * Array property generic setter
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->array[$name] = $value;
    }

    /**
     * Array property generic unsetter
     *
     * @param string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        unset($this->array[$name]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->array);
    }

    /**
     * Array offset checker
     *
     * @param int|string $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        $result = $this->__isset((string) $offset);
        return $result;
    }

    /**
     * Array offset getter
     *
     * @param int|string $offset
     * @param mixed $default [OPTIONAL]
     * @return mixed
     */
    public function offsetGet($offset, $default = null): mixed
    {
        if ($this->__isset((string) $offset) === true) {
            $result = $this->__get((string) $offset);
        } elseif (func_num_args() > 1) {
            $result = $default;
        } else {
            $message = sprintf('Missing item "[%s]"', $offset);
            throw new InvalidArgumentException($message);
        }
        return $result;
    }

    /**
     * Array offset setter
     *
     * @param int|string $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->__set((string) $offset, $value);
    }

    /**
     * Array offset unsetter
     *
     * @param int|string $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->__unset((string) $offset);
    }

    // <editor-fold defaultstate="collapsed" desc="Getters & Setters">
    /**
     * Array getter
     *
     * @return TInnerArray
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * Array setter
     *
     * @param TInnerArray $array
     * @return static Provides fluent interface
     */
    protected function setArray(array $array)
    {
        $this->array = $array;
        return $this;
    }

    // </editor-fold>
}
