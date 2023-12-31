<?php

namespace Crell\AttributeUtils\Attributes\Functions;

use Crell\AttributeUtils\HasSubAttributes;

#[\Attribute(\Attribute::TARGET_FUNCTION)]
class SubParent implements HasSubAttributes
{
    public readonly SubChild $child;

    public function __construct(public readonly string $a = 'A') {}

    public function subAttributes(): array
    {
        return [SubChild::class => 'fromSubChild'];
    }

    public function fromSubChild(?SubChild $child): void
    {
        $this->child = $child;
    }
}
