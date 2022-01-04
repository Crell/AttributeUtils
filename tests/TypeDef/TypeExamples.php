<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\TypeDef;

use Crell\AttributeUtils\I1;
use Crell\AttributeUtils\I2;
use Crell\AttributeUtils\OtherClass;
use Crell\AttributeUtils\SomeClass;

class TypeExamples
{
    public function simpleInt(): int
    {
    }

    public function simpleString(): string
    {
    }

    public function simpleStringNullable(): ?string
    {
    }

    public function simpleStringNullableUnion(): string|null
    {
    }

    public function simpleArray(): array
    {
    }

    public function simpleClass(): OtherClass
    {
    }

    public function simpleVoid(): void
    {
    }

    public function simpleNever(): never
    {
    }

    public function returnsSelfChild(): self
    {
    }

    public function returnsStatic(): static
    {
    }

    public function scalarUnion(): int|string
    {
    }

    public function mixedUnion(): SomeClass|string
    {
    }

    // Curiously, PHP lets us define this type but it is impossible.
    public function intersection(): SomeClass&OtherClass
    {
    }

    public function interfaceIntersection(): I1&I2
    {
    }

    public function mixedReturn(): mixed
    {
    }

    public function noReturnType()
    {
    }
}
