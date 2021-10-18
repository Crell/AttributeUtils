<?php

declare(strict_types=1);

namespace Crell\Serde\Benchmarks;

use Crell\AttributeUtils\Analyzer;
use Crell\AttributeUtils\Attributes\BasicClass;
use Crell\AttributeUtils\Attributes\ClassWithProperties;
use Crell\AttributeUtils\Attributes\ClassWithPropertiesWithSubAttributes;
use Crell\AttributeUtils\Attributes\ClassWithReflectableProperties;
use Crell\AttributeUtils\Attributes\ClassWithReflection;
use Crell\AttributeUtils\Attributes\InheritableClassAttributeMain;
use Crell\AttributeUtils\Attributes\TransitiveClassAttribute;
use Crell\AttributeUtils\Records\AttributesInheritChild;
use Crell\AttributeUtils\Records\ClassWithCustomizedFields;
use Crell\AttributeUtils\Records\ClassWithCustomizedPropertiesExcludeByDefault;
use Crell\AttributeUtils\Records\ClassWithDefaultFields;
use Crell\AttributeUtils\Records\ClassWithPropertiesWithReflection;
use Crell\AttributeUtils\Records\ClassWithSubAttributes;
use Crell\AttributeUtils\Records\NoProps;
use Crell\AttributeUtils\Records\NoPropsOverride;
use Crell\AttributeUtils\Records\Point;
use Crell\AttributeUtils\Records\TransitiveFieldClass;
use PhpBench\Benchmark\Metadata\Annotations\AfterMethods;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;

/**
 * @Revs(100)
 * @Iterations(10)
 * @Warmup(2)
 * @BeforeMethods({"setUp"})
 * @AfterMethods({"tearDown"})
 * @OutputTimeUnit("milliseconds", precision=3)
 */
class ClassAnalyzerBench
{
    protected Analyzer $analyzer;

    public function setUp(): void
    {
        $this->analyzer = new Analyzer();
    }

    public function tearDown(): void {}

    public function benchPoint(): void
    {
        $this->analyzer->analyze(Point::class, BasicClass::class);
    }

    public function benchReflectionNoOverride(): void
    {
        $this->analyzer->analyze(NoProps::class, ClassWithReflection::class);
    }

    public function benchReflectionWithOverride(): void
    {
        $this->analyzer->analyze(NoPropsOverride::class, ClassWithReflection::class);
    }

    public function benchFieldableDefault(): void
    {
        $this->analyzer->analyze(ClassWithDefaultFields::class, ClassWithProperties::class);
    }

    public function benchFieldableCustomized(): void
    {
        $this->analyzer->analyze(ClassWithCustomizedFields::class, ClassWithProperties::class);
    }

    public function benchFieldableDefaultNoInclude(): void
    {
        $this->analyzer->analyze(ClassWithCustomizedPropertiesExcludeByDefault::class, ClassWithProperties::class);
    }

    public function benchFieldableReflectableFields(): void
    {
        $this->analyzer->analyze(ClassWithPropertiesWithReflection::class, ClassWithReflectableProperties::class);
    }

    public function benchFieldableWithSubAttributes(): void
    {
        $this->analyzer->analyze(ClassWithSubAttributes::class, ClassWithPropertiesWithSubAttributes::class);
    }

    public function benchAttributesAndSubAttributesInherit(): void
    {
        $this->analyzer->analyze(AttributesInheritChild::class, InheritableClassAttributeMain::class);
    }

    public function benchTransitiveFieldsInherit(): void
    {
        $this->analyzer->analyze(TransitiveFieldClass::class, TransitiveClassAttribute::class);
    }



}
