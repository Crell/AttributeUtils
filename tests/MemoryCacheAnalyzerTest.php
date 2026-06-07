<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\TestCase;

#[Medium]
class MemoryCacheAnalyzerTest extends TestCase
{
    use ClassAnalyzerCacheTestMethods;

    public function getTestSubject(): ClassAnalyzer
    {
        return new MemoryCacheAnalyzer($this->getMockAnalyzer());
    }
}
