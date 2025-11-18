<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Components;

use Crell\AttributeUtils\AttributeParser;

class ReadProperties implements Component
{
    public function __construct(
        public readonly string $attribute,
        public readonly \Closure $callback,
        public readonly bool $includeByDefault = true,
    ) {}

    public function read(object $attribute, AttributeParser $parser): void
    {

    }

    public function components(\Reflector $subject): array
    {
        return array_filter($subject->getProperties(), static fn (\ReflectionProperty $r) => !$r->isStatic());
    }
}
