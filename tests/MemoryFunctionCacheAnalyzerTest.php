<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use PHPUnit\Framework\TestCase;

class MemoryFunctionCacheAnalyzerTest extends TestCase
{
    use FunctionAnalyzerCacheTestMethods;

    public function getTestSubject(): FunctionAnalyzer
    {
        return new MemoryCacheFunctionAnalyzer($this->getMockAnalyzer());
    }
}
