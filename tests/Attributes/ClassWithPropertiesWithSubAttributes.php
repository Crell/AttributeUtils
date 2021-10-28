<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\HasSubAttributes;
use Crell\AttributeUtils\ParseProperties;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassWithPropertiesWithSubAttributes implements ParseProperties, HasSubAttributes
{
    public array $properties = [];

    public string $c;

    public function __construct(
        public bool $include = true,
    ) {}

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function includePropertiesByDefault(): bool
    {
        return $this->include;
    }

    public function propertyAttribute(): string
    {
        return PropertyWithSubAttributes::class;
    }

    public function subAttributes(): array
    {
        return [ClassSubAttribute::class => 'fromSubAttribute'];
    }

    public function fromSubAttribute(?ClassSubAttribute $sub): void
    {
        $sub ??= new ClassSubAttribute();
        $this->c = $sub->c;
    }
}
