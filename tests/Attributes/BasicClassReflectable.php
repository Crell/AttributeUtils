<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\FromReflectionClass;

/**
 * The most basic class-level attribute. No fancy integration at all.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class BasicClassReflectable implements FromReflectionClass
{
    public function __construct(
        public int $a = 0,
        public int $b = 0,
        public ?string $name = null,
    ) {}

    public function fromReflection(\ReflectionClass|\ReflectionObject $subject): void
    {
        $this->name ??= $subject->getShortName();
    }
}
