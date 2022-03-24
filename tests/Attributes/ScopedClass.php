<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Attributes\Reflect\CollectMethods;
use Crell\AttributeUtils\Attributes\Reflect\CollectProperties;
use Crell\AttributeUtils\HasSubAttributes;
use Crell\AttributeUtils\ParseMethods;
use Crell\AttributeUtils\ParseProperties;
use Crell\AttributeUtils\SupportsScopes;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ScopedClass implements HasSubAttributes, ParseProperties, ParseMethods, SupportsScopes
{
    use CollectProperties;
    use CollectMethods;

    public ?ScopedClassSub $sub;

    /**
     * @var ScopedClassSubMulti[]
     */
    public array $multi;

    public function __construct(
        public string $val = 'Z',
        public ?string $scope = null,
        public bool $includeInAll = true,
    ) {}

    public function scopes(): array
    {
        return [$this->scope];
    }

    public function subAttributes(): array
    {
        return [
            ScopedClassSub::class => 'fromSingleSub',
            ScopedClassSubMulti::class => 'fromMultiSub',
        ];
    }

    public function fromSingleSub(?ScopedClassSub $sub): void
    {
        $this->sub = $sub;
    }

    public function fromMultiSub(array $subs): void
    {
        $this->multi = $subs;
    }

    public function methodAttribute(): string
    {
        return ScopedMethod::class;
    }

    public function includePropertiesByDefault(): bool
    {
        return true;
    }

    public function propertyAttribute(): string
    {
        return ScopedProperty::class;
    }
}
