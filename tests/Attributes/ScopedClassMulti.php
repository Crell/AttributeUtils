<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Attributes\Reflect\CollectMethods;
use Crell\AttributeUtils\Attributes\Reflect\CollectProperties;
use Crell\AttributeUtils\ParseProperties;
use Crell\AttributeUtils\SupportsScopes;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ScopedClassMulti implements ParseProperties, SupportsScopes
{
    use CollectProperties;

    public ?ScopedClassSub $sub;

    /**
     * @var ScopedClassSubMulti[]
     */
    public array $multi;

    public function __construct(
        public string $val = 'Z',
        public array $scopes = [null],
        public bool $includeInAll = true,
    ) {}

    public function scopes(): array
    {
        return $this->scopes;
    }

    public function propertyAttribute(): string
    {
        return ScopedPropertyMulti::class;
    }

    public function includePropertiesByDefault(): bool
    {
        return false;
    }
}
