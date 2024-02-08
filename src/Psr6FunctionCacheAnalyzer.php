<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Psr\Cache\CacheItemPoolInterface;

class Psr6FunctionCacheAnalyzer implements FunctionAnalyzer
{
    public function __construct(
        private readonly FunctionAnalyzer $analyzer,
        private readonly CacheItemPoolInterface $pool,
    ) {}

    public function analyze(\Closure|string $function, string $attribute, array $scopes = []): object
    {
        // We cannot cache a closure, as we have no reliable identifier for it.
        if ($function instanceof \Closure) {
            return $this->analyzer->analyze($function, $attribute, $scopes);
        }

        $key = $this->buildKey($function, $attribute, $scopes);

        $item = $this->pool->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        }

        // No expiration; the cached data would only need to change
        // if the source code changes.
        $value = $this->analyzer->analyze($function, $attribute, $scopes);
        $item->set($value);
        $this->pool->save($item);
        return $value;
    }

    /**
     * Generates the cache key for this request.
     *
     * @param array<string|null> $scopes
     *   The scopes for which this analysis should run.
     */
    private function buildKey(string $function, string $attribute, array $scopes): string
    {
        $parts = [
            $function,
            $attribute,
            implode(',', $scopes),
        ];

        return str_replace('\\', '_', \implode('-', $parts));
    }
}
