<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\InterfaceAttributes;

#[RealName(first: 'Bruce', last: 'Wayne')]
#[Alias('Batman')]
#[Alias('The Dark Knight')]
#[Alias('The Caped Crusader')]
class Hero
{
}
