<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

trait CollectEnumCases
{
    /** @var ReflectEnumCase[] */
    public readonly array $cases;

    public function setCases(array $cases): void
    {
        $this->cases = $cases;
    }

    public function includeCasesByDefault(): bool
    {
        return true;
    }

    abstract public function caseAttribute(): string;
}
