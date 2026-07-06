<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260703193519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_document (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, year INT DEFAULT NULL, researcher VARCHAR(255) DEFAULT NULL, doi VARCHAR(255) DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E52701AD166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE youtube_media (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, youtube_id VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_document ADD CONSTRAINT FK_E52701AD166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clipping ADD summary_pt LONGTEXT DEFAULT NULL, ADD summary_en LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD registration_link VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_opportunity ADD pdf_name VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_document DROP FOREIGN KEY FK_E52701AD166D1F9C');
        $this->addSql('DROP TABLE project_document');
        $this->addSql('DROP TABLE youtube_media');
        $this->addSql('ALTER TABLE clipping DROP summary_pt, DROP summary_en');
        $this->addSql('ALTER TABLE event DROP registration_link');
        $this->addSql('ALTER TABLE job_opportunity DROP pdf_name, DROP updated_at');
    }
}
