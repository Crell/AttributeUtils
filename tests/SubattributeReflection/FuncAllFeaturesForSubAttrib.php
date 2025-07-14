<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\SubattributeReflection;

use Crell\AttributeUtils\Attributes\Reflect\CollectParameters;
use Crell\AttributeUtils\HasSubAttributes;
use Crell\AttributeUtils\ParseParameters;

#[\Attribute(\Attribute::TARGET_FUNCTION)]
class FuncAllFeaturesForSubAttrib implements HasSubAttributes, ParseParameters
{
    use CollectParameters;

    /** @var ComponentAttribute[] */
    public readonly array $parameters;

    public ?SubAttributeReflect $sub;

    public function subAttributes(): array
    {
        return [
            SubAttributeReflect::class => fn(?SubAttributeReflect $sub) => $this->sub = $sub,
        ];
    }

    public function parameterAttribute(): string
    {
        return ComponentAttribute::class;
    }

}
