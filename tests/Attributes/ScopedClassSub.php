<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\SupportsScopes;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ScopedClassSub implements SupportsScopes
{
    public function __construct(
        public string $val = 'Z',
        public ?string $scope = null,
        public bool $includeInAll = true,
    ) {}

    public function scopes(): array
    {
        return [$this->scope];
    }
}
