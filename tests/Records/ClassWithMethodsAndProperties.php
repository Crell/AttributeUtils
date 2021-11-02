<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\MethodWithReflection;
use Crell\AttributeUtils\Attributes\ParameterWithReflection;

class ClassWithMethodsAndProperties
{
    protected string $c;

    public function __construct(
        public string $a = '',
        public string $b = '',
    ) {}

    #[MethodWithReflection(a: 'z', b: 'y', name: 'beep')]
    private function methodOne(int $one, #[ParameterWithReflection(x: 3, y: 4, name: 'beep')] float $two): string
    {
        return '';
    }

    private function methodTwo(#[ParameterWithReflection(x: 5, y: 6)] bool $three, string $four): static
    {
        return $this;
    }
}
