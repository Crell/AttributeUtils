<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;


class ClassWithMethodsAndPropertiesChild extends ClassWithMethodsAndProperties
{
    private function methodTwo(bool $three, string $four): static
    {
        return $this;
    }
}
