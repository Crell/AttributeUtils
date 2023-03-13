<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Psr\Cache\CacheItemPoolInterface;

/**
 * PSR-6 bridge for caching the analyzer.
 */
class Psr6CacheAnalyzer implements ClassAnalyzer
{
    public function __construct(
        private ClassAnalyzer $analyzer,
        private CacheItemPoolInterface $pool,
    ) {}

    public function analyze(object|string $class, string $attribute, array $scopes = []): object
    {
        $key = $this->buildKey($class, $attribute, $scopes);

        $item = $this->pool->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        }

        // No expiration; the cached data would only need to change
        // if the source code changes.
        $value = $this->analyzer->analyze($class, $attribute, $scopes);
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
    private function buildKey(object|string $class, string $attribute, array $scopes): string
    {
        $parts = [
            is_object($class) ? $class::class : $class,
            $attribute,
            implode(',', $scopes),
        ];

        return str_replace('\\', '_', \implode('-', $parts));
    }
}
