<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\FromReflectionClass;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassWithReflection implements FromReflectionClass
{
    public function __construct(
        public int $a = 0,
        public int $b = 0,
        public ?string $name = null,
    ) {}

    public function fromReflection(\ReflectionClass $subject): void
    {
        $this->name ??= $subject->getShortName();
    }
}
