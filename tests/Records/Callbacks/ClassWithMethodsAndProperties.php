<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records\Callbacks;

use Crell\AttributeUtils\Attributes\MethodWithReflection;

class ClassWithMethodsAndProperties
{
    public string $fullName;

    public function __construct(
        public string $first = '',
        public string $last = '',
    ) {}

    #[MethodWithReflection(a: 'z', b: 'y', name: 'beep')]
    private function methodOne(): void
    {
        $this->fullName = "$this->first $this->last";
    }

    private function methodTwo(): void
    {
        $this->fullName = "$this->first $this->last";
    }
}
