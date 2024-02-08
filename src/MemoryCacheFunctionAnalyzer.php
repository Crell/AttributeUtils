<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * A simple in-memory cache for function analyzers.
 */
class MemoryCacheFunctionAnalyzer implements FunctionAnalyzer
{
    public function __construct(
        private readonly FunctionAnalyzer $analyzer,
    ) {}

    public function analyze(string|\Closure $function, string $attribute, array $scopes = []): object
    {
        // We cannot cache a closure, as we have no reliable identifier for it.
        if ($function instanceof \Closure) {
            return $this->analyzer->analyze($function, $attribute, $scopes);
        }

        $scopekey = '';
        if ($scopes) {
            sort($scopes);
            $scopekey = implode(',', $scopes);
        }

        return $this->cache[$function][$attribute][$scopekey] ??= $this->analyzer->analyze($function, $attribute, $scopes);
    }
}
