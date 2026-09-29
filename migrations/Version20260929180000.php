<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260929180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajustes set/2026: linhas e módulos de pesquisa, geolocalização de parceiros, instituição de pesquisadores, galeria de notícias e inscrições em eventos';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE research_area (id INT AUTO_INCREMENT NOT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE research_line (id INT AUTO_INCREMENT NOT NULL, area_id INT NOT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, position INT DEFAULT 0 NOT NULL, INDEX IDX_84A48426BD0F409C (area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE research_module (id INT AUTO_INCREMENT NOT NULL, line_id INT NOT NULL, name_pt VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, position INT DEFAULT 0 NOT NULL, INDEX IDX_ABC5862B4D7B7542 (line_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news_image (id INT AUTO_INCREMENT NOT NULL, news_id INT NOT NULL, image_id INT DEFAULT NULL, caption_pt VARCHAR(255) DEFAULT NULL, caption_en VARCHAR(255) DEFAULT NULL, position INT DEFAULT 0 NOT NULL, INDEX IDX_BF828301B5A459A0 (news_id), INDEX IDX_BF8283013DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_registration (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, name VARCHAR(255) NOT NULL, institution VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(40) NOT NULL, file_name VARCHAR(255) DEFAULT NULL, original_file_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8FBBAD5471F7E88B (event_id), INDEX IDX_8FBBAD548B8E8428 (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE research_line ADD CONSTRAINT FK_84A48426BD0F409C FOREIGN KEY (area_id) REFERENCES research_area (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE research_module ADD CONSTRAINT FK_ABC5862B4D7B7542 FOREIGN KEY (line_id) REFERENCES research_line (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news_image ADD CONSTRAINT FK_BF828301B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news_image ADD CONSTRAINT FK_BF8283013DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE event_registration ADD CONSTRAINT FK_8FBBAD5471F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE home_banner CHANGE subtitle_pt subtitle_pt VARCHAR(255) DEFAULT NULL, CHANGE subtitle_en subtitle_en VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE project ADD research_line_id INT DEFAULT NULL, ADD research_module_id INT DEFAULT NULL, DROP modulo_pt, DROP modulo_en');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EED4D5C558 FOREIGN KEY (research_line_id) REFERENCES research_line (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EED48E6568 FOREIGN KEY (research_module_id) REFERENCES research_module (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2FB3D0EED4D5C558 ON project (research_line_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EED48E6568 ON project (research_module_id)');

        $this->addSql('ALTER TABLE partner ADD acronym VARCHAR(50) DEFAULT NULL, ADD country VARCHAR(2) DEFAULT \'BR\' NOT NULL, ADD city VARCHAR(120) DEFAULT NULL, ADD latitude NUMERIC(10, 7) DEFAULT NULL, ADD longitude NUMERIC(10, 7) DEFAULT NULL');
        $this->addSql("UPDATE partner SET country = CASE
            WHEN region = 'BR' THEN 'BR'
            WHEN name LIKE '%França%' THEN 'FR'
            WHEN name LIKE '%Espanha%' THEN 'ES'
            WHEN name LIKE '%Portugal%' THEN 'PT'
            WHEN name LIKE '%Austrália%' THEN 'AU'
            WHEN name LIKE '%Inglaterra%' OR name LIKE '%Reino Unido%' THEN 'GB'
            ELSE 'US' END");
        $this->addSql('ALTER TABLE partner DROP region');

        $this->addSql('ALTER TABLE researcher ADD institution VARCHAR(255) DEFAULT NULL, CHANGE area_pt area_pt VARCHAR(255) DEFAULT NULL, CHANGE area_en area_en VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD registration_open TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EED4D5C558');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EED48E6568');
        $this->addSql('ALTER TABLE news_image DROP FOREIGN KEY FK_BF828301B5A459A0');
        $this->addSql('ALTER TABLE news_image DROP FOREIGN KEY FK_BF8283013DA5256D');
        $this->addSql('ALTER TABLE event_registration DROP FOREIGN KEY FK_8FBBAD5471F7E88B');
        $this->addSql('ALTER TABLE research_line DROP FOREIGN KEY FK_84A48426BD0F409C');
        $this->addSql('ALTER TABLE research_module DROP FOREIGN KEY FK_ABC5862B4D7B7542');
        $this->addSql('DROP TABLE research_area');
        $this->addSql('DROP TABLE news_image');
        $this->addSql('DROP TABLE event_registration');
        $this->addSql('DROP TABLE research_line');
        $this->addSql('DROP TABLE research_module');
        $this->addSql('UPDATE home_banner SET subtitle_pt = COALESCE(subtitle_pt, \'\'), subtitle_en = COALESCE(subtitle_en, \'\')');
        $this->addSql('ALTER TABLE home_banner CHANGE subtitle_pt subtitle_pt VARCHAR(255) NOT NULL, CHANGE subtitle_en subtitle_en VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX IDX_2FB3D0EED4D5C558 ON project');
        $this->addSql('DROP INDEX IDX_2FB3D0EED48E6568 ON project');
        $this->addSql('ALTER TABLE project ADD modulo_pt VARCHAR(255) DEFAULT \'\' NOT NULL, ADD modulo_en VARCHAR(255) DEFAULT \'\' NOT NULL, DROP research_line_id, DROP research_module_id');
        $this->addSql('ALTER TABLE partner ADD region VARCHAR(10) DEFAULT \'BR\' NOT NULL');
        $this->addSql("UPDATE partner SET region = IF(country = 'BR', 'BR', 'INT')");
        $this->addSql('ALTER TABLE partner DROP acronym, DROP country, DROP city, DROP latitude, DROP longitude');
        $this->addSql('UPDATE researcher SET area_pt = COALESCE(area_pt, \'\'), area_en = COALESCE(area_en, \'\')');
        $this->addSql('ALTER TABLE researcher DROP institution, CHANGE area_pt area_pt VARCHAR(255) NOT NULL, CHANGE area_en area_en VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE event DROP registration_open');
    }
}
