<?php

namespace Crell\AttributeUtils;

use PHPUnit\Framework\TestCase;

class MemoryCacheFunctionAnalyzerTest extends TestCase
{
    use FunctionAnalyzerCacheTestMethods;

    public function getTestSubject(): FunctionAnalyzer
    {
        return new MemoryCacheFunctionAnalyzer($this->getMockAnalyzer());
    }
}
