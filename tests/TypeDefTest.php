<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\AttributeUtils\TypeDef\Suit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TypeDefTest extends TestCase
{
    #[Test, DataProvider('typeDefProvider')]
    public function typedefs(string $methodName, callable $test): void
    {
        $rType = (new \ReflectionClass(TypeDef\TypeExamples::class))
            ->getMethod($methodName)
            ->getReturnType();

        $def = new TypeDef($rType);

        $test($def);
    }

    #[Test, RequiresPhp('>=8.2')]
    public function analyze_dnf_attributes(): void
    {
        $rType = (new \ReflectionClass(TypeDef\TypeExamples82::class))
            ->getMethod('returnsDnf')
            ->getReturnType();

        $def = new TypeDef($rType);

        static::assertFalse($def->allowsNull);
        static::assertFalse($def->isSimple());
        static::assertEquals(TypeComplexity::Compound, $def->complexity);
        static::assertTrue($def->accepts('int'));
        static::assertTrue($def->accepts(Implementer::class));
        static::assertFalse($def->accepts(SomeClass::class));
    }

    public static function typeDefProvider(): iterable
    {
        yield 'simpleInt' => [
            'methodName' => 'simpleInt',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('int', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('int'));
                static::assertTrue($typeDef->accepts(get_debug_type(1)));
                static::assertFalse($typeDef->accepts('string'));
            },
        ];

        yield 'simpleString' => [
            'methodName' => 'simpleString',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('string', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('string'));
                static::assertTrue($typeDef->accepts(get_debug_type('hi')));
                static::assertFalse($typeDef->accepts('float'));
            },
        ];

        yield 'simpleStringNullable' => [
            'methodName' => 'simpleStringNullable',
            'test' => static function (TypeDef $typeDef) {
                static::assertTrue($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('string', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('string'));
                static::assertTrue($typeDef->accepts(get_debug_type('hi')));
                static::assertTrue($typeDef->accepts('null'));
                static::assertTrue($typeDef->accepts(get_debug_type(null)));
            },
        ];

        yield 'simpleStringNullableUnion' => [
            'methodName' => 'simpleStringNullableUnion',
            'test' => static function (TypeDef $typeDef) {
                static::assertTrue($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('string', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('string'));
                static::assertTrue($typeDef->accepts(get_debug_type('hi')));
                static::assertTrue($typeDef->accepts('null'));
                static::assertTrue($typeDef->accepts(get_debug_type(null)));
            },
        ];

        yield 'simpleArray' => [
            'methodName' => 'simpleArray',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('array', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('array'));
                static::assertTrue($typeDef->accepts(get_debug_type([1, 2])));
            },
        ];

        yield 'simpleVoid' => [
            'methodName' => 'simpleVoid',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('void', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('void'));
                static::assertFalse($typeDef->accepts('string'));
            },
        ];

        yield 'simpleNever' => [
            'methodName' => 'simpleNever',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('never', $typeDef->getSimpleType());
            },
        ];

        yield 'returnsStatic' => [
            'methodName' => 'returnsStatic',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('static', $typeDef->getSimpleType());
                // @todo accepts() doesn't work with this yet.
            },
        ];

        yield 'simpleClass' => [
            'methodName' => 'simpleClass',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals(OtherClass::class, $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts(OtherClass::class));
                static::assertFalse($typeDef->accepts(SomeClass::class));
            },
        ];

        yield 'scalarUnion' => [
            'methodName' => 'scalarUnion',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Union, $typeDef->complexity);
                static::assertEquals(['string', 'int'], $typeDef->getUnionTypes());
                static::assertTrue($typeDef->accepts('int'));
                static::assertTrue($typeDef->accepts('string'));
                static::assertFalse($typeDef->accepts(SomeClass::class));
            },
        ];

        yield 'mixedUnion' => [
            'methodName' => 'mixedUnion',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Union, $typeDef->complexity);
                static::assertEquals([SomeClass::class, 'string'], $typeDef->getUnionTypes());
                static::assertTrue($typeDef->accepts(SomeClass::class));
                static::assertTrue($typeDef->accepts('string'));
                static::assertFalse($typeDef->accepts(OtherClass::class));
            },
        ];

        yield 'intersection' => [
            'methodName' => 'intersection',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Intersection, $typeDef->complexity);
                static::assertFalse($typeDef->accepts(SomeClass::class));
                static::assertFalse($typeDef->accepts(OtherClass::class));
            },
        ];

        yield 'interfaceIntersection' => [
            'methodName' => 'interfaceIntersection',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Intersection, $typeDef->complexity);
                static::assertTrue($typeDef->accepts(Implementer::class));
                static::assertFalse($typeDef->accepts(IncompleteImplementer::class));
                static::assertFalse($typeDef->accepts(OtherClass::class));
            },
        ];

        yield 'mixedReturn' => [
            'methodName' => 'mixedReturn',
            'test' => static function (TypeDef $typeDef) {
                static::assertTrue($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Simple, $typeDef->complexity);
                static::assertTrue($typeDef->accepts('string'));
                static::assertTrue($typeDef->accepts(Implementer::class));
                static::assertTrue($typeDef->accepts(IncompleteImplementer::class));
                static::assertTrue($typeDef->accepts(OtherClass::class));
            },
        ];

        yield 'noReturnType' => [
            'methodName' => 'noReturnType',
            'test' => static function (TypeDef $typeDef) {
                static::assertTrue($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Simple, $typeDef->complexity);
                static::assertTrue($typeDef->accepts('string'));
                static::assertTrue($typeDef->accepts(Implementer::class));
                static::assertTrue($typeDef->accepts(IncompleteImplementer::class));
                static::assertTrue($typeDef->accepts(OtherClass::class));
            },
        ];

        yield 'returnsEnum' => [
            'methodName' => 'returnsEnum',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Simple, $typeDef->complexity);
                static::assertTrue($typeDef->accepts(Suit::class));
                static::assertFalse($typeDef->accepts('spades'));
            },
        ];
    }
}

// These can stay here as they are syntax compatible with all supported PHP versions.
// The other test classes parse error on 8.0 and earlier, so need to be in separate
// files, even if this test class is skipped.

class SomeClass {}

class OtherClass {}

interface I1 {}

interface I2 {}

class Implementer implements I1, I2 {}

class IncompleteImplementer implements I1 {}
