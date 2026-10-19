<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261019120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Projetos: objetivo e descrição passam a ser opcionais';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project CHANGE objetivo_pt objetivo_pt VARCHAR(255) DEFAULT NULL, CHANGE objetivo_en objetivo_en VARCHAR(255) DEFAULT NULL, CHANGE descricao_pt descricao_pt LONGTEXT DEFAULT NULL, CHANGE descricao_en descricao_en LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE project SET objetivo_pt = COALESCE(objetivo_pt, \'\'), objetivo_en = COALESCE(objetivo_en, \'\'), descricao_pt = COALESCE(descricao_pt, \'\'), descricao_en = COALESCE(descricao_en, \'\')');
        $this->addSql('ALTER TABLE project CHANGE objetivo_pt objetivo_pt VARCHAR(255) NOT NULL, CHANGE objetivo_en objetivo_en VARCHAR(255) NOT NULL, CHANGE descricao_pt descricao_pt LONGTEXT NOT NULL, CHANGE descricao_en descricao_en LONGTEXT NOT NULL');
    }
}
