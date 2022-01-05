<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface ParseEnumCases
{
    public function setCases(array $cases): void;

    public function includeCasesByDefault(): bool;

    public function caseAttribute(): string;
}
