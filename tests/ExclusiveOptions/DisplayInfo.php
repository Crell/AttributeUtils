<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\ExclusiveOptions;

use Crell\AttributeUtils\HasSubAttributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class DisplayInfo implements HasSubAttributes
{
    public readonly ?DisplayType $type;

    public function subAttributes(): array
    {
        return [DisplayType::class => 'fromDisplayType'];
    }

    public function fromDisplayType(?DisplayType $type): void
    {
        $this->type = $type;
    }
}
