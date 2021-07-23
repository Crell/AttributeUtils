<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

/**
 * A simple in-memory result cache for ClassAnalyzers.
 */
class MemoryCacheAnalyzer implements ClassAnalyzer
{
    /**
     * @var array<string, <string, object>>>
     */
    private array $cache = [];

    public function __construct(private ClassAnalyzer $analyzer)
    {}

    public function analyze(object|string $class, string $attribute): object
    {
        $key = is_object($class) ? $class::class : $class;

        return $this->cache[$key][$attribute] ??= $this->analyzer->analyze($class, $attribute);
    }
}
