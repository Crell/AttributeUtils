<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\HasSubAttributes;
use Crell\AttributeUtils\Inheritable;
use Crell\AttributeUtils\TransitiveProperty;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class TransitivePropertyAttribute implements TransitiveProperty, Inheritable, HasSubAttributes
{
    public ?TransitivePropertySubAttribute $sub;

    public function __construct(
        public string $beep = 'default',
    ) {}

    public function subAttributes(): array
    {
        return [TransitivePropertySubAttribute::class => 'setter'];
    }

    public function setter(?TransitivePropertySubAttribute $sub): void
    {
        $this->sub = $sub;
    }
}
