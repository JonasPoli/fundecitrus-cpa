<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261112205721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Projetos: pesquisadores adicionais (project_researcher)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_researcher (project_id INT NOT NULL, researcher_id INT NOT NULL, INDEX IDX_6D5955EC166D1F9C (project_id), INDEX IDX_6D5955ECC7533BDE (researcher_id), PRIMARY KEY(project_id, researcher_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_researcher ADD CONSTRAINT FK_6D5955EC166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_researcher ADD CONSTRAINT FK_6D5955ECC7533BDE FOREIGN KEY (researcher_id) REFERENCES researcher (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_researcher DROP FOREIGN KEY FK_6D5955EC166D1F9C');
        $this->addSql('ALTER TABLE project_researcher DROP FOREIGN KEY FK_6D5955ECC7533BDE');
        $this->addSql('DROP TABLE project_researcher');
    }
}
