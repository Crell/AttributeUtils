<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\AttributeUtils\Attributes\ClassWithProperties;
use Crell\AttributeUtils\Attributes\ScopedClass;
use Crell\AttributeUtils\Records\ClassWithDefaultFields;
use Crell\AttributeUtils\Records\ClassWithScopes;
use Crell\AttributeUtils\Records\Point;
use PHPUnit\Framework\Attributes\Test;

/**
 * Use this trait in a test class for each cache implementation.
 */
trait ClassAnalyzerCacheTestMethods
{
    abstract public function getTestSubject(): ClassAnalyzer;

    protected function getMockAnalyzer(): ClassAnalyzer
    {
        return new class implements ClassAnalyzer {
            public function analyze(object|string $class, string $attribute, array $scopes = []): object
            {
                $key = is_object($class) ? $class::class : $class;

                // Every call to this method returns a new garbage object.
                // Since attributes can be anything, that's fine. We just
                // want a unique object each time. Capturing the parameters
                // is just for extra verification.
                return new class($key, $attribute, $scopes) {
                    public function __construct(
                        public string $key,
                        public string $attribute,
                        public array $scope,
                    ) {}
                };
            }
        };
    }

    #[Test]
    public function cache_analysis(): void
    {
        $analyzer = $this->getTestSubject();

        $def1 = $analyzer->analyze(Point::class, ClassWithProperties::class);
        $def2 = $analyzer->analyze(Point::class, ClassWithProperties::class);
        $def3 = $analyzer->analyze(Point::class, ClassWithDefaultFields::class);

        self::assertSame($def1, $def2);
        self::assertNotSame($def1, $def3);
    }

    #[Test]
    public function cache_analysis_scopes(): void
    {
        $analyzer = $this->getTestSubject();

        $def1 = $analyzer->analyze(ClassWithScopes::class, ScopedClass::class);
        $def2 = $analyzer->analyze(ClassWithScopes::class, ScopedClass::class);
        $def3 = $analyzer->analyze(ClassWithScopes::class, ScopedClass::class, scopes: ['One']);

        self::assertSame($def1, $def2);
        self::assertNotSame($def1, $def3);
    }
}
