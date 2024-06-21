<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Crell\AttributeUtils\HasSubAttributes;

#[\Attribute]
class ClosureSubAttributeMain implements HasSubAttributes
{
    public readonly ?ClosureSubAttributeReferenced $referenced;
    public readonly ?ClosureSubAttributeInline $inline;

    public function subAttributes(): array
    {
        return [
            ClosureSubAttributeReferenced::class => $this->fromReferenced(...),
            ClosureSubAttributeInline::class => function(?ClosureSubAttributeInline $other) {
                $this->inline = $other;
            }
        ];
    }

    private function fromReferenced(?ClosureSubAttributeReferenced $other): void
    {
        $this->referenced ??= $other;
    }
}
