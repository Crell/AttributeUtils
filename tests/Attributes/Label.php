<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Crell\AttributeUtils\Excludable;
use Crell\AttributeUtils\SupportsScopes;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Label implements SupportsScopes, Excludable
{
    public function __construct(
        public readonly string $name = 'Untitled',
        public readonly ?string $language = null,
        public readonly bool $exclude = false,
    ) {}

    public function scopes(): array
    {
        return [$this->language];
    }

    public function exclude(): bool
    {
        return $this->exclude;
    }
}
