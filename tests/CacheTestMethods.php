<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\AttributeUtils\Attributes\ClassWithProperties;
use Crell\AttributeUtils\Records\ClassWithDefaultFields;
use Crell\AttributeUtils\Records\Point;

/**
 * Use this trait in a test class for each cache implementation.
 */
trait CacheTestMethods
{
    abstract public function getTestSubject(): ClassAnalyzer;

    protected function getMockAnalyzer(): ClassAnalyzer
    {
        return new class implements ClassAnalyzer {
            public function analyze(object|string $class, string $attribute): object
            {
                $key = is_object($class) ? $class::class : $class;

                // Every call to this method returns a new garbage object.
                // Since attributes can be anything, that's fine. We just
                // want a unique object each time. Capturing the parameters
                // is just for extra verification.
                return new class($key, $attribute) {
                    public function __construct(public string $key, public string $attribute) {}
                };
            }
        };
    }

    /**
     * @test
     */
    public function cache_analysis(): void
    {

        $analyzer = $this->getTestSubject();

        $def1 = $analyzer->analyze(Point::class, ClassWithProperties::class);
        $def2 = $analyzer->analyze(Point::class, ClassWithProperties::class);
        $def3 = $analyzer->analyze(Point::class, ClassWithDefaultFields::class);

        self::assertSame($def1, $def2);
        self::assertNotSame($def1, $def3);
    }
}
