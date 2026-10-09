<?php

namespace App\Enum;

enum ResearcherCategory: string
{
    case RESEARCHER = 'pesquisador';
    case POSTDOC_FELLOW = 'bolsista_pos_doutorado';
    case PHD_FELLOW = 'bolsista_doutorado';
    case MASTERS_FELLOW = 'bolsista_mestrado';
    case UNDERGRADUATE_FELLOW = 'bolsista_iniciacao_cientifica';
    case TRAINING_FELLOW = 'bolsista_treinamento_tecnico';
    case TECHNICAL_SUPPORT = 'apoio_tecnico';

    public const GROUP_RESEARCHERS = 'pesquisadores';
    public const GROUP_FELLOWS = 'bolsistas';
    public const GROUP_SUPPORT = 'apoio';

    public function label(string $locale = 'pt'): string
    {
        $en = $locale === 'en';

        return match ($this) {
            self::RESEARCHER => $en ? 'Researchers' : 'Pesquisadores',
            self::POSTDOC_FELLOW => $en ? 'Postdoctoral' : 'Pós-doutorado',
            self::PHD_FELLOW => $en ? 'PhD' : 'Doutorado',
            self::MASTERS_FELLOW => $en ? "Master's" : 'Mestrado',
            self::UNDERGRADUATE_FELLOW => $en ? 'Undergraduate research' : 'Iniciação científica',
            self::TRAINING_FELLOW => $en ? 'Technical training' : 'Treinamento técnico',
            self::TECHNICAL_SUPPORT => $en ? 'Technical support' : 'Apoio técnico',
        };
    }

    public function adminLabel(): string
    {
        return match ($this->group()) {
            self::GROUP_RESEARCHERS => 'Pesquisador',
            self::GROUP_FELLOWS => 'Bolsista — ' . $this->label(),
            default => $this->label(),
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::RESEARCHER => self::GROUP_RESEARCHERS,
            self::TECHNICAL_SUPPORT => self::GROUP_SUPPORT,
            default => self::GROUP_FELLOWS,
        };
    }

    public static function groupLabel(string $group, string $locale = 'pt'): string
    {
        $en = $locale === 'en';

        return match ($group) {
            self::GROUP_FELLOWS => $en ? 'Fellows' : 'Bolsistas',
            self::GROUP_SUPPORT => $en ? 'Technical support' : 'Apoio técnico',
            default => $en ? 'Researchers' : 'Pesquisadores',
        };
    }
}
