<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\Label;
use Crell\AttributeUtils\Attributes\Labeled;

#[Labeled]
class LabeledApp
{
    #[Label(name: 'Installation')]
    #[Label(name: 'Instalación', language: 'es')]
    public string $install;

    #[Label(name: 'Setup')]
    #[Label(name: 'Configurar', language: 'es')]
    #[Label(name: 'Einrichten', language: 'de')]
    public string $setup;

    #[Label(name: 'Einloggen', language: 'de')]
    #[Label(language: 'fr', exclude: true)]
    public string $login;

    public string $customization;
}
