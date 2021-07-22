<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\ReflectionPopulatable;

/**
 * A basic property attribute.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class BasicPropertyReflectable implements ReflectionPopulatable
{
    public function __construct(
        public string $a = 'a',
        public string $b = 'b',
        public ?string $name = null,
    ) {}

    /**
     * @param \ReflectionProperty $subject
     */
    public function fromReflection(\Reflector $subject): void
    {
        $this->name ??= $subject->getName();
    }
}
