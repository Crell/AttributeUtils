<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Excludable;
use Crell\AttributeUtils\SupportsScopes;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ScopedPropertyMulti implements SupportsScopes, Excludable
{
    public function __construct(
        public string $val = 'Z',
        public array $scopes = [null],
        public bool $includeUnscopedInScope = true,
        public bool $exclude = false,
    ) {
    }

    public function scopes(): array
    {
        return $this->scopes;
    }

    public function exclude(): bool
    {
        return $this->exclude;
    }
}
