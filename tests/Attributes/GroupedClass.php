<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Attributes\Reflect\CollectMethods;
use Crell\AttributeUtils\Attributes\Reflect\CollectProperties;
use Crell\AttributeUtils\HasSubAttributes;
use Crell\AttributeUtils\ParseMethods;
use Crell\AttributeUtils\ParseProperties;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class GroupedClass implements HasSubAttributes, ParseProperties, ParseMethods
{
    use CollectProperties;
    use CollectMethods;

    public GroupedClassSub $sub;

    /**
     * @var GroupedClassSubMulti[]
     */
    public array $multi;

    public function __construct(
        public string $val = 'Z',
        public ?string $group = null,
    ) {}

    public function subAttributes(): array
    {
        return [
            GroupedClassSub::class => 'fromSingleSub',
            GroupedClassSubMulti::class => 'fromMultiSub',
        ];
    }

    public function fromSingleSub(?GroupedClassSub $sub): void
    {
        $this->sub = $sub;
    }

    public function fromMultiSub(array $subs): void
    {
        $this->multi = $subs;
    }

    public function methodAttribute(): string
    {
        return GroupedMethod::class;
    }

    public function propertyAttribute(): string
    {
        return GroupedProperty::class;
    }
}
