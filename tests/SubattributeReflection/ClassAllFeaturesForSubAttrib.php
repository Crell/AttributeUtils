<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\SubattributeReflection;

use Attribute;
use Crell\AttributeUtils\Attributes\Reflect\CollectClassConstants;
use Crell\AttributeUtils\Attributes\Reflect\CollectEnumCases;
use Crell\AttributeUtils\Attributes\Reflect\CollectMethods;
use Crell\AttributeUtils\Attributes\Reflect\CollectProperties;
use Crell\AttributeUtils\HasSubAttributes;
use Crell\AttributeUtils\ParseClassConstants;
use Crell\AttributeUtils\ParseEnumCases;
use Crell\AttributeUtils\ParseMethods;
use Crell\AttributeUtils\ParseProperties;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassAllFeaturesForSubAttrib implements
    ParseMethods,
    ParseProperties,
    ParseClassConstants,
    ParseEnumCases,
    HasSubAttributes
{
    use CollectMethods;
    use CollectProperties;
    use CollectEnumCases;
    use CollectClassConstants;

    /** @var ComponentAttribute[] */
    public readonly array $properties;

    /** @var ComponentAttribute[] */
    public readonly array $methods;

    /** @var ComponentAttribute[] */
    public readonly array $constants;

    /** @var ComponentAttribute[] */
    public readonly array $cases;

    public ?SubAttributeReflect $sub;

    public function __construct(
        public string $a = 'A',
    ) {}

    public function subAttributes(): array
    {
        return [
            SubAttributeReflect::class => fn(?SubAttributeReflect $sub) => $this->sub = $sub,
        ];
    }

    public function caseAttribute(): string
    {
        return ComponentAttribute::class;
    }

    public function methodAttribute(): string
    {
        return ComponentAttribute::class;
    }

    public function parameterAttribute(): string
    {
        return ComponentAttribute::class;
    }

    public function propertyAttribute(): string
    {
        return ComponentAttribute::class;
    }

    public function constantAttribute(): string
    {
        return ComponentAttribute::class;
    }
}
