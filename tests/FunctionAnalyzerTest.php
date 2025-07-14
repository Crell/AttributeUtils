<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\AttributeUtils\Attributes\Functions\HasParameters;
use Crell\AttributeUtils\Attributes\Functions\IncludesReflection;
use Crell\AttributeUtils\Attributes\Functions\ParameterAttrib;
use Crell\AttributeUtils\Attributes\Functions\RequiredArg;
use Crell\AttributeUtils\Attributes\Functions\SubChild;
use Crell\AttributeUtils\Attributes\Functions\SubParent;
use Crell\AttributeUtils\SubattributeReflection\ClassAllFeaturesForSubAttrib;
use Crell\AttributeUtils\SubattributeReflection\ClassWithAllFeaturesForSubAttribReflection;
use Crell\AttributeUtils\SubattributeReflection\ComponentAttribute;
use Crell\AttributeUtils\SubattributeReflection\EnumForSubAttrib;
use Crell\AttributeUtils\SubattributeReflection\FuncAllFeaturesForSubAttrib;
use Crell\AttributeUtils\SubattributeReflection\SubAttributeReflect;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

// @phpstan-ignore-next-line
#[RequiredArg]
function required_attribute_arguments_missing() {}

#[IncludesReflection]
function from_reflection() {}

#[SubParent]
#[SubChild(b: 'Override')]
function has_sub_attributes() {}

#[HasParameters(ParameterAttrib::class)]
function has_parameters(
    #[ParameterAttrib] int $first,
    #[ParameterAttrib('Override')] int $second,
) {}


#[FuncAllFeaturesForSubAttrib, SubAttributeReflect]
function func_for_subattrib(
    #[ComponentAttribute, SubAttributeReflect] string $parameter,
) {}

class FunctionAnalyzerTest extends TestCase
{
    protected string $ns;

    public function setUp(): void
    {
        $this->ns = __NAMESPACE__;
    }

    #[Test, DataProvider('attributeTestProvider')]
    public function analyze_functions(string|\Closure $subject, string $attribute, callable $test): void
    {
        $analyzer = new FuncAnalyzer();

        $classDef = $analyzer->analyze($subject, $attribute);

        $test($classDef);
    }

    #[Test]
    public function missing_required_fields(): void
    {
        $this->expectException(RequiredAttributeArgumentsMissing::class);
        $analyzer = new FuncAnalyzer();

        $analyzer->analyze("{$this->ns}\\required_attribute_arguments_missing", Attributes\Functions\RequiredArg::class);
    }

    /**
     * @see analyze_classes()
     */
    public static function attributeTestProvider(): \Generator
    {
        $ns = __NAMESPACE__ . '\\';

        yield 'Includes Reflection' => [
            'subject' => "{$ns}from_reflection",
            'attribute' => IncludesReflection::class,
            'test' => static function(IncludesReflection $funcDef) use ($ns) {
                static::assertEquals("{$ns}from_reflection", $funcDef->name);
            },
        ];

        yield 'Includes Sub-attribute' => [
            'subject' => "{$ns}has_sub_attributes",
            'attribute' => SubParent::class,
            'test' => static function(SubParent $funcDef) use ($ns) {
                static::assertEquals('Override', $funcDef->child->b);
            },
        ];

        yield 'Includes parameters' => [
            'subject' => "{$ns}has_parameters",
            'attribute' => HasParameters::class,
            'test' => static function(HasParameters $funcDef) use ($ns) {
                static::assertEquals('default', $funcDef->parameters['first']->a);
                static::assertEquals('Override', $funcDef->parameters['second']->a);
            },
        ];

        yield 'Closure' => [
            'subject' => #[HasParameters(ParameterAttrib::class)] fn (#[ParameterAttrib] int $first,
                             #[ParameterAttrib('Override')] int $second,
            ): int => $first * $second,
            'attribute' => HasParameters::class,
            'test' => static function(HasParameters $funcDef) use ($ns) {
                static::assertEquals('default', $funcDef->parameters['first']->a);
                static::assertEquals('Override', $funcDef->parameters['second']->a);
            },
        ];

        yield 'Subattributes with FromReflection (function)' => [
            'subject' => "{$ns}func_for_subattrib",
            'attribute' => FuncAllFeaturesForSubAttrib::class,
            'test' => static function(FuncAllFeaturesForSubAttrib $funcDef) use ($ns) {
                static::assertEquals("{$ns}func_for_subattrib", $funcDef->sub->name);
                static::assertEquals('parameter', $funcDef->parameters['parameter']->sub->name);
            },
        ];
    }
}
