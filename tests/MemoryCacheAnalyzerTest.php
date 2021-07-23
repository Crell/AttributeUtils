<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\AttributeUtils\Attributes\ClassWithProperties;
use Crell\AttributeUtils\Records\ClassWithDefaultFields;
use Crell\AttributeUtils\Records\Point;
use PHPUnit\Framework\TestCase;

class MemoryCacheAnalyzerTest extends TestCase
{
    /**
     * @test
     */
    public function cache_analysis(): void
    {
        $mockAnalyzer = new class implements ClassAnalyzer {
            public function analyze(object|string $class, string $attribute): object
            {
                $key = is_object($class) ? $class::class : $class;

                return new class($key, $attribute) {
                    public function __construct(public string $key, public string $attribute) {}
                };
            }
        };

        $analyzer = new MemoryCacheAnalyzer($mockAnalyzer);

        $def1 = $analyzer->analyze(Point::class, ClassWithProperties::class);
        $def2 = $analyzer->analyze(Point::class, ClassWithProperties::class);
        $def3 = $analyzer->analyze(Point::class, ClassWithDefaultFields::class);

        self::assertSame($def1, $def2);
        self::assertNotSame($def1, $def3);
    }
}
