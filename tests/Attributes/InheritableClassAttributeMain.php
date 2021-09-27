<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\HasSubAttributes;
use Crell\AttributeUtils\Inheritable;

#[Attribute(Attribute::TARGET_CLASS)]
class InheritableClassAttributeMain implements HasSubAttributes, Inheritable
{
    public ?InheritableClassSubAttribute $sub;

    public function __construct(
        public int $a = 1,
    ) {}

    public function subAttributes(): array
    {
        return [InheritableClassSubAttribute::class => 'setter'];
    }

    public function setter(?InheritableClassSubAttribute $attrib): void
    {
        $this->sub = $attrib;
    }

}
