<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Fig\Cache\Memory\MemoryPool;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\TestCase;

#[Medium]
class Psr6CacheAnalyzerTest extends TestCase
{
    use ClassAnalyzerCacheTestMethods;

    public function getTestSubject(): ClassAnalyzer
    {
        return new Psr6CacheAnalyzer($this->getMockAnalyzer(), new MemoryPool());
    }
}
