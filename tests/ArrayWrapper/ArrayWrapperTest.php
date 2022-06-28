<?php

declare(strict_types=1);

namespace ZeleznyPa\ArrayWrapper;

use BadMethodCallException;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function count;
use function sprintf;

/**
 * @coversDefaultClass ZeleznyPa\ArrayWrapper\ArrayWrapper
 */
class ArrayWrapperTest extends TestCase
{
    // <editor-fold defaultstate="collapsed" desc="Integration tests">
    /**
     * Tester of undefined method call exception
     *
     * @return void
     * @group negative
     * @covers ::__call
     */
    public function testCallUndefinedMethod(): void
    {
        $arrayWrapper = ArrayWrapper::create();
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(sprintf('Call to undefined method %s::undefinedMethod()', ArrayWrapper::class));
        /* @phpstan-ignore-next-line */
        $arrayWrapper->undefinedMethod();
    }

    /**
     * Tester of count implementation
     *
     * @return void
     * @covers ::count
     */
    public function testCount(): void
    {
        $array = ['value'];
        $arrayWrapper = ArrayWrapper::create($array);
        self::assertCount(count($array), $arrayWrapper);
    }

    /**
     * Tester of Static factory implementation
     *
     * @return void
     * @group integration
     * @covers ::create
     */
    public function testCreate(): void
    {
        $array = [];
        $arrayWrapperOne = ArrayWrapper::create($array);
        $arrayWrapperTwo = ArrayWrapper::create($array);
        self::assertNotSame($arrayWrapperOne, $arrayWrapperTwo);
    }

    /**
     * Tester of magic getters setters implementation
     *
     * @return void
     * @group integration
     * @covers ::__call
     */
    public function testGettersSettersAccess(): void
    {
        $value = 'value';
        /** @var ArrayWrapper<array{key: string}> */
        $arrayWrapper = ArrayWrapper::create();
        self::assertFalse($arrayWrapper->isKey());
        self::assertFalse($arrayWrapper->hasKey());
        $arrayWrapper->setKey($value);
        self::assertTrue($arrayWrapper->isKey());
        self::assertTrue($arrayWrapper->hasKey());
        self::assertEquals($value, $arrayWrapper->getKey());
        $arrayWrapper->unsetKey();
        self::assertFalse($arrayWrapper->isKey());
        self::assertFalse($arrayWrapper->hasKey());
    }

    /**
     * Tester of offset methods access implementation
     *
     * @return void
     * @group integration
     * @covers ::offsetExists
     * @covers ::offsetGet
     * @covers ::offsetSet
     * @covers ::offsetUnset
     */
    public function testOffsetAccess(): void
    {
        $offset = 'key';
        $value = 'value';
        $arrayWrapper = ArrayWrapper::create();
        self::assertFalse(isset($arrayWrapper[$offset]));
        $arrayWrapper[$offset] = $value;
        self::assertTrue(isset($arrayWrapper[$offset]));
        self::assertSame($value, $arrayWrapper[$offset]);
        unset($arrayWrapper[$offset]);
        self::assertFalse(isset($arrayWrapper[$offset]));
    }

    /**
     * Tester of offset methods default value access implementation
     *
     * @return void
     * @group integration
     * @covers ::offsetGet
     */
    public function testOffsetDefaultAccess(): void
    {
        $offset = 'key';
        $default = 'value';
        $arrayWrapper = ArrayWrapper::create();
        self::assertFalse($arrayWrapper->offsetExists($offset));
        self::assertSame($default, $arrayWrapper->offsetGet($offset, $default));
    }

    /**
     * Tester of offset methods not exist value access implementation
     *
     * @return void
     * @group negative
     * @covers ::offsetGet
     */
    public function testOffsetNotExistAccess(): void
    {
        $offset = 'key';
        $arrayWrapper = ArrayWrapper::create();
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage(sprintf('Missing item "[%s]"', $offset));
        $arrayWrapper->offsetGet($offset);
    }

    /**
     * Tester of properties access implementation
     *
     * @return void
     * @group integration
     * @covers ::__get
     * @covers ::__isset
     * @covers ::__set
     * @covers ::__unset
     */
    public function testPropertiesAccess(): void
    {
        $offset = 'key';
        $value = 'value';
        $arrayWrapper = ArrayWrapper::create();
        self::assertFalse(isset($arrayWrapper->{$offset}));
        /* @phpstan-ignore-next-line */
        $arrayWrapper->{$offset} = $value;
        self::assertTrue(isset($arrayWrapper->{$offset}));
        self::assertSame($value, $arrayWrapper->{$offset});
        unset($arrayWrapper->{$offset});
        self::assertFalse(isset($arrayWrapper->{$offset}));
    }

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="Unit Tests">
    /**
     * Tester of Constructor implementation
     *
     * @return void
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $array = [];
        $arrayWrapper = $this->createArrayWrapperMock(['setArray']);
        $arrayWrapper->expects(self::once())->method('setArray')->with(self::equalTo($array));
        $arrayWrapper->__construct($array);
    }

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="Helpers">
    /**
     * ArrayWrapper mock factory
     *
     * @param string[] $methods [OPTIONAL] List of mocked method names
     * @return ArrayWrapper<array<mixed>>&MockObject
     */
    protected function createArrayWrapperMock(array $methods = []): MockObject
    {
        $mock = $this->createPartialMock(ArrayWrapper::class, $methods);
        return $mock;
    }

    // </editor-fold>
}
