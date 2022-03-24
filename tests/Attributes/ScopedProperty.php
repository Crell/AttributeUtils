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
    ) {}

    public function scopes(): array
    {
        return [$this->scope];

        /*
        return match ([$scope, $this->scope]) {
            [null, null] => true,
            [null, $this->scope] => false,
            [$scope, null] => $this->includeUnscopedInScope,
            default => $this->scope === $scope,
        };
        */
    }

    public function includeUnscopedInScope(): bool
    {
        return $this->includeUnscopedInScope;
    }


    public function exclude(): bool
    {
        return $this->exclude;
    }
}
