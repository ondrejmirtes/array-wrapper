<?php

declare(strict_types=1);

namespace ZeleznyPa\ArrayWrapper\PHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use ZeleznyPa\ArrayWrapper\ArrayWrapper;
use function lcfirst;
use function preg_match;

class ArrayWrapperMethodsClassReflectionExtension implements MethodsClassReflectionExtension
{

	public function hasMethod(ClassReflection $classReflection, string $methodName): bool
	{
		$arrayWrapper = $classReflection->getAncestorWithClassName(ArrayWrapper::class);
		if ($arrayWrapper === null) {
			return false;
		}

		$map = $arrayWrapper->getActiveTemplateTypeMap();
		$array = $map->getType('TInnerArray');
		if ($array === null) {
			return false;
		}

		if (!$array instanceof ConstantArrayType) {
			return false;
		}

		$result = preg_match('~^(?P<method>(?:get|has|is|set|unset))(?P<offset>.*)$~', $methodName, $match);
		if ($result !== 1) {
			return false;
		}
		if (!isset($match['method'])) {
			return false;
		}
		if (isset($match['offset'])) {
			return $array->hasOffsetValueType(new ConstantStringType(lcfirst($match['offset'])))->yes();
		}

		return false;
	}

	public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
	{
		$arrayWrapper = $classReflection->getAncestorWithClassName(ArrayWrapper::class);
		if ($arrayWrapper === null) {
			throw new \InvalidArgumentException();
		}

		$map = $arrayWrapper->getActiveTemplateTypeMap();
		$array = $map->getType('TInnerArray');
		if ($array === null) {
			throw new \InvalidArgumentException();
		}

		if (!$array instanceof ConstantArrayType) {
			throw new \InvalidArgumentException();
		}

		$result = preg_match('~^(?P<method>(?:get|has|is|set|unset))(?P<offset>.*)$~', $methodName, $match);
		if ($result !== 1) {
			throw new \InvalidArgumentException();
		}
		if (!isset($match['method'])) {
			throw new \InvalidArgumentException();
		}
		if (!isset($match['offset'])) {
			throw new \InvalidArgumentException();
		}

		$method = $match['method'];
		$offset = lcfirst($match['offset']);
		if (!$array->hasOffsetValueType(new ConstantStringType($offset))->yes()) {
			throw new \InvalidArgumentException();
		}

		if ($method === 'get') {
			return new GetMethodReflection($classReflection, $offset, $array->getOffsetValueType(new ConstantStringType($offset)));
		} elseif (($method === 'has') || ($method === 'is')) {
			return new HasMethodReflection($classReflection, $offset);
		} elseif ($method === 'set') {
			return new SetMethodReflection($classReflection, $offset, $array->getOffsetValueType(new ConstantStringType($offset)));
		} elseif ($method === 'unset') {
			return new UnsetMethodReflection($classReflection, $offset);
		}

		throw new \InvalidArgumentException();
	}

}
