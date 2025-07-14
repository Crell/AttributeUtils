<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\HasSubAttributes;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassWithOwnSubAttributes implements HasSubAttributes
{
    public string $c;

    public function __construct(
        public bool $include = true,
    ) {}

    public function subAttributes(): array
    {
        return [ClassSubAttribute::class => 'fromSubAttribute'];
    }

    public function fromSubAttribute(?ClassSubAttribute $sub): void
    {
        $sub ??= new ClassSubAttribute();
        $this->c = $sub->c;
    }
}
