<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\HasSubAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ConfigurablePropertyWithSubAttributes implements HasSubAttributes
{
    public ?object $subattrib;

    public function __construct(
        public string $subattribute = PropertySubAttribute::class,
        public string $a = 'A',
    ) {}

    public function subAttributes(): array
    {
        return [$this->subattribute => 'fromSubAttribute'];
    }

    public function fromSubAttribute(?object $sub): void
    {
        $sub ??= new ($this->subattribute)();
        $this->subattrib = $sub;
    }
}
