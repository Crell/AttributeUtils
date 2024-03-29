<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use PHPUnit\Framework\TestCase;

class MemoryCacheAnalyzerTest extends TestCase
{
    use ClassAnalyzerCacheTestMethods;

    public function getTestSubject(): ClassAnalyzer
    {
        return new MemoryCacheAnalyzer($this->getMockAnalyzer());
    }
}
