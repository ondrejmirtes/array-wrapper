<?php

namespace ZeleznyPa\ArrayWrapper;

use function PHPStan\Testing\assertType;

function (string $s): void {
	$a = new ArrayWrapper(['foo' => $s]);
	assertType('ZeleznyPa\ArrayWrapper\ArrayWrapper<array{foo: string}>', $a);

	assertType('string', $a->getFoo());
	assertType('bool', $a->isFoo());
	assertType('bool', $a->hasFoo());
};
