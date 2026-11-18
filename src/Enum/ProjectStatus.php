<?php

namespace App\Enum;

enum ProjectStatus: int
{
    case IN_PROGRESS = 1;
    case COMPLETED = 2;

    public function label(string $locale = 'pt'): string
    {
        $en = $locale === 'en';

        return match ($this) {
            self::IN_PROGRESS => $en ? 'Active / In progress' : 'Ativo / Em andamento',
            self::COMPLETED => $en ? 'Completed' : 'Concluído',
        };
    }

    public function isOngoing(): bool
    {
        return $this === self::IN_PROGRESS;
    }
}
