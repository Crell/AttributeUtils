<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Excludable;
use Crell\AttributeUtils\SupportsScopes;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ScopedProperty implements SupportsScopes, Excludable
{
    public function __construct(
        public string $val = 'Z',
        public ?string $scope = null,
        public bool $includeUnscopedInScope = true,
        public bool $exclude = false,
    ) {
    }

    public function scopes(): array
    {
        if ($this->includeUnscopedInScope) {
            return [$this->scope, null];
        }
        return [$this->scope];
    }

    public function exclude(): bool
    {
        return $this->exclude;
    }
}
