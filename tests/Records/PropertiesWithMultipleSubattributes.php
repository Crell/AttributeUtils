<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\GenericClass;
use Crell\AttributeUtils\Attributes\MultiSubAttribute;
use Crell\AttributeUtils\Attributes\PropertyWithMultiSubAttributes;

#[GenericClass(PropertyWithMultiSubAttributes::class)]
class PropertiesWithMultipleSubattributes
{
    public function __construct(
        #[PropertyWithMultiSubAttributes(name: 'Main')]
        #[MultiSubAttribute(name: 'first')]
        #[MultiSubAttribute(name: 'second')]
        public string $name = '',
    ) {}
}
