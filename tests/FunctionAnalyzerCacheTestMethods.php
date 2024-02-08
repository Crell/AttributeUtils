<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\AttributeUtils\Attributes\ClassWithProperties;
use Crell\AttributeUtils\Attributes\Functions\IncludesReflection;
use Crell\AttributeUtils\Attributes\Functions\SubParent;
use Crell\AttributeUtils\Attributes\ScopedClass;
use Crell\AttributeUtils\Records\ClassWithDefaultFields;
use Crell\AttributeUtils\Records\ClassWithScopes;
use Crell\AttributeUtils\Records\Point;
use PHPUnit\Framework\Attributes\Test;

#[IncludesReflection]
function test_function() {}

/**
 * Use this trait in a test class for each cache implementation.
 */
trait FunctionAnalyzerCacheTestMethods
{
    abstract public function getTestSubject(): FunctionAnalyzer;

    protected function getMockAnalyzer(): FunctionAnalyzer
    {
        return new class implements FunctionAnalyzer {
            public function analyze(string|\Closure $function, string $attribute, array $scopes = []): object
            {
                // Every call to this method returns a new garbage object.
                // Since attributes can be anything, that's fine. We just
                // want a unique object each time. Capturing the parameters
                // is just for extra verification.
                return new class($attribute, $scopes) {
                    public function __construct(
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

        $ns = __NAMESPACE__ . '\\';

        $def1 = $analyzer->analyze("{$ns}test_function", IncludesReflection::class);
        $def2 = $analyzer->analyze("{$ns}test_function", IncludesReflection::class);
        $def3 = $analyzer->analyze("{$ns}test_function", SubParent::class);

        self::assertSame($def1, $def2);
        self::assertNotSame($def1, $def3);
    }

    #[Test]
    public function cache_analysis_scopes(): void
    {
        $analyzer = $this->getTestSubject();

        $ns = __NAMESPACE__ . '\\';

        $def1 = $analyzer->analyze("{$ns}test_function", IncludesReflection::class);
        $def2 = $analyzer->analyze("{$ns}test_function", IncludesReflection::class);
        $def3 = $analyzer->analyze("{$ns}test_function", IncludesReflection::class, scopes: ['One']);

        self::assertSame($def1, $def2);
        self::assertNotSame($def1, $def3);
    }
}
