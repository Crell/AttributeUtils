<?php

declare(strict_types=1);

use Crell\AttributeUtils\Analyzer;
use Crell\AttributeUtils\Attributes\BasicClass;
use Crell\AttributeUtils\Attributes\ClassWithProperties;
use Crell\AttributeUtils\Attributes\ClassWithPropertiesWithSubAttributes;
use Crell\AttributeUtils\Attributes\ClassWithReflectableProperties;
use Crell\AttributeUtils\Attributes\ClassWithReflection;
use Crell\AttributeUtils\Attributes\InheritableClassAttributeMain;
use Crell\AttributeUtils\Attributes\TransitiveClassAttribute;
use Crell\AttributeUtils\MemoryCacheAnalyzer;
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

require 'vendor/autoload.php';

function run(): void
{
    $analyzer = new MemoryCacheAnalyzer(new Analyzer());

    $analyzer->analyze(Point::class, BasicClass::class);
    $analyzer->analyze(NoProps::class, ClassWithReflection::class);
    $analyzer->analyze(NoPropsOverride::class, ClassWithReflection::class);
    $analyzer->analyze(ClassWithDefaultFields::class, ClassWithProperties::class);
    $analyzer->analyze(ClassWithCustomizedFields::class, ClassWithProperties::class);
    $analyzer->analyze(ClassWithCustomizedPropertiesExcludeByDefault::class, ClassWithProperties::class);
    $analyzer->analyze(ClassWithPropertiesWithReflection::class, ClassWithReflectableProperties::class);
    $analyzer->analyze(ClassWithSubAttributes::class, ClassWithPropertiesWithSubAttributes::class);
    $analyzer->analyze(AttributesInheritChild::class, InheritableClassAttributeMain::class);
    $analyzer->analyze(TransitiveFieldClass::class, TransitiveClassAttribute::class);
}

for ($i=0; $i < 100; ++$i) {
    run();
}
