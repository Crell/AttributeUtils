<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\HasSubAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyWithSubAttributes implements HasSubAttributes
{
    public string $b;

    public function __construct(
        public string $a = 'A',
    ) {}

    public function subAttributes(): array
    {
        return [PropertySubAttribute::class => 'fromSubAttribute'];
    }

    public function fromSubAttribute(?PropertySubAttribute $sub): void
    {
        $sub ??= new PropertySubAttribute();
        $this->b = $sub->b;
    }

}
