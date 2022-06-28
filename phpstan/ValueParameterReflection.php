<?php

declare(strict_types=1);

namespace ZeleznyPa\ArrayWrapper\PHPStan;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\Type;

class ValueParameterReflection implements ParameterReflection
{

	public function __construct(
		private Type $offsetType,
	)
	{
	}

	public function getName(): string
	{
		return 'value';
	}

	public function isOptional(): bool
	{
		return false;
	}

	public function getType(): Type
	{
		return $this->offsetType;
	}

	public function passedByReference(): PassedByReference
	{
		return PassedByReference::createNo();
	}

	public function isVariadic(): bool
	{
		return false;
	}

	public function getDefaultValue(): ?Type
	{
		return null;
	}

}
