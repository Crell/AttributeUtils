<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * A simple in-memory result cache for ClassAnalyzers.
 */
class MemoryCacheAnalyzer implements ClassAnalyzer
{
    /**
     * @var array<string, array<string, object>>
     */
    private array $cache = [];

    public function __construct(private ClassAnalyzer $analyzer)
    {}

    public function analyze(object|string $class, string $attribute, ?string $scope = null): object
    {
        $key = is_object($class) ? $class::class : $class;

        return $this->cache[$key][$attribute][$scope ?? ''] ??= $this->analyzer->analyze($class, $attribute, $scope);
    }
}
