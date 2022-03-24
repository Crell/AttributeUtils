<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Attributes\Reflect\CollectParameters;
use Crell\AttributeUtils\ParseParameters;
use Crell\AttributeUtils\SupportsScopes;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ScopedMethod implements SupportsScopes, ParseParameters
{
    use CollectParameters;

    public function __construct(
        public string $val = 'Z',
        public ?string $scope = null,
        public bool $includeInAll = true,
    ) {}

    public function scopes(): array
    {
        return [$this->scope];
    }

    public function parameterAttribute(): string
    {
        return ScopedParam::class;
    }
}
