<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260702210026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news ADD youtube_video_code VARCHAR(255) DEFAULT NULL, ADD seo_title VARCHAR(255) DEFAULT NULL, ADD seo_description VARCHAR(255) DEFAULT NULL, ADD image_alt VARCHAR(255) DEFAULT NULL, ADD canonical_url VARCHAR(255) DEFAULT NULL, ADD is_no_index TINYINT(1) DEFAULT 0 NOT NULL, ADD status VARCHAR(255) DEFAULT NULL, ADD highlighted TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news DROP youtube_video_code, DROP seo_title, DROP seo_description, DROP image_alt, DROP canonical_url, DROP is_no_index, DROP status, DROP highlighted');
    }
}
