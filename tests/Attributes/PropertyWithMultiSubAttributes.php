<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\HasSubAttributes;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyWithMultiSubAttributes implements HasSubAttributes
{
    public array $subs;

    public function __construct(
        public string $name = '',
    ) {}

    public function subAttributes(): array
    {
        return [MultiSubAttribute::class => 'fromSubAttribute'];
    }

    public function fromSubAttribute(array $subs): void
    {
        $this->subs = $subs;
    }
}
