<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Fig\Cache\Memory\MemoryPool;
use PHPUnit\Framework\TestCase;

class Psr6FunctionCacheAnalyzerTest extends TestCase
{
    use FunctionAnalyzerCacheTestMethods;

    public function getTestSubject(): FunctionAnalyzer
    {
        return new Psr6FunctionCacheAnalyzer($this->getMockAnalyzer(), new MemoryPool());
    }
}
