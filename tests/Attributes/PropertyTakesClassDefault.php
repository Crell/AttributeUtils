<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\ReadsClass;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyTakesClassDefault implements ReadsClass
{
    public function __construct(
        public int $a = 0,
        public int $b = 0,
    ) {}

    /**
     * @param PropertyTakesClassDefaultClass $class
     */
    public function fromClassAttribute(object $class): void
    {
        if (! $this->a) {
            $this->a = $class->a;
        }
        if (! $this->b) {
            $this->b = $class->b;
        }
    }
}
