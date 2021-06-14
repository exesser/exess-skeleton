<?php declare(strict_types=1);

namespace ExEss\Cms\Doctrine\Type;

use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

class LinkTo extends AbstractEnumType
{
    public const DASHBOARD = 'dashboard';
    public const GUIDANCE_MODE = 'guidance_mode';
    public const FOCUS_MODE = 'focus_mode';

    public static function getValues(): array
    {
        return [
            self::DASHBOARD => 'dashboard',
            self::GUIDANCE_MODE => 'guidance-mode',
            self::FOCUS_MODE => 'focus-mode',
        ];
    }

    public function getName(): string
    {
        return 'enum_link_to';
    }
}
