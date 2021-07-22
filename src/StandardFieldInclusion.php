<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

trait StandardFieldInclusion
{
//    protected array $include = [];

//    protected array $exclude = [];

    protected bool $inclusionPolicy = true;

    public function includeProperties(): array
    {
        return $this->include;
    }

    public function excludeProperties(): array
    {
        return $this->exclude;
    }

    public function includeByDefault(): bool
    {
        return $this->inclusionPolicy;
    }
}
