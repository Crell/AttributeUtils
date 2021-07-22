<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\ReflectionPopulatable;

/**
 * The most basic class-level attribute. No fancy integration at all.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class BasicClassReflectable implements ReflectionPopulatable
{
    public function __construct(
        public int $a = 0,
        public int $b = 0,
        public ?string $name = null,
    ) {}

    /**
     * @param \ReflectionClass|\ReflectionObject $subject
     */
    public function fromReflection(\Reflector $subject): void
    {
        $this->name ??= $subject->getShortName();
    }
}
