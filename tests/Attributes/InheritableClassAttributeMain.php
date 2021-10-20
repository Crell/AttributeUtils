<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\HasSubAttributes;
use Crell\AttributeUtils\Inheritable;
use Crell\AttributeUtils\ParseProperties;

#[Attribute(Attribute::TARGET_CLASS)]
class InheritableClassAttributeMain implements HasSubAttributes, Inheritable, ParseProperties
{
    public ?InheritableClassSubAttribute $sub;

    public array $properties = [];

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

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function includePropertiesByDefault(): bool
    {
        return true;
    }

    public static function propertyAttribute(): string
    {
        return InheritablePropertyAttributeMain::class;
    }
}
