<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

use Crell\ObjectAnalyzer\Attributes\ClassWithProperties;
use Crell\ObjectAnalyzer\Records\ClassWithDefaultFields;
use Crell\ObjectAnalyzer\Records\Point;
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
