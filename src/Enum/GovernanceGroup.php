<?php

namespace App\Enum;

enum GovernanceGroup: string
{
    case FUNDERS = 'mantenedores';
    case COORDINATION = 'coordenacao';
    case PROJECT_MANAGEMENT = 'gestao_projetos';
    case ADMINISTRATIVE_COMMITTEE = 'comite_administrativo';
    case EXECUTIVE_COMMITTEE = 'comite_executivo';
    case SECTOR_BOARD = 'conselho_setor';
    case INTERNATIONAL_BOARD = 'conselho_internacional';

    public function label(string $locale = 'pt'): string
    {
        $en = $locale === 'en';

        return match ($this) {
            self::FUNDERS => $en ? 'Funders' : 'Mantenedores',
            self::COORDINATION => $en ? 'Coordination' : 'Coordenação',
            self::PROJECT_MANAGEMENT => $en ? 'Project management' : 'Gestão de projetos',
            self::ADMINISTRATIVE_COMMITTEE => $en ? 'Administrative and financial committee' : 'Comitê administrativo e financeiro',
            self::EXECUTIVE_COMMITTEE => $en ? 'Executive committee' : 'Comitê executivo',
            self::SECTOR_BOARD => $en ? 'Citrus sector advisory board' : 'Conselho consultivo do setor citrícola',
            self::INTERNATIONAL_BOARD => $en ? 'International advisory board' : 'Conselho consultivo internacional',
        };
    }
}
