<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\HasSubAttributes;
use Crell\AttributeUtils\Inheritable;

#[Attribute(Attribute::TARGET_CLASS)]
class InheritableClassSubAttributeMain implements HasSubAttributes
{
    public ?InheritableClassSubAttribute $sub;

    public function subAttributes(): array
    {
        return [InheritableClassSubAttribute::class => 'setter'];
    }

    public function setter(?InheritableClassSubAttribute $attrib): void
    {
        $this->sub = $attrib;
    }

}
