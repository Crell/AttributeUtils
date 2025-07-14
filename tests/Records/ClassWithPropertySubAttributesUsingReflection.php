<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ConfigurableClassWithProperties;
use Crell\AttributeUtils\Attributes\ConfigurablePropertyWithSubAttributes;
use Crell\AttributeUtils\Attributes\PropertySubAttributeWithReflection;

#[ConfigurableClassWithProperties(propertyAttribute: ConfigurablePropertyWithSubAttributes::class)]
class ClassWithPropertySubAttributesUsingReflection
{
    public function __construct(
        #[ConfigurablePropertyWithSubAttributes(PropertySubAttributeWithReflection::class)]
        #[PropertySubAttributeWithReflection]
        public string $prop = 'A',
    ) {}
}
