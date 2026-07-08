<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260708210857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clipping (id INT AUTO_INCREMENT NOT NULL, veiculo VARCHAR(255) NOT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, summary_pt LONGTEXT DEFAULT NULL, summary_en LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, folder_pt VARCHAR(255) NOT NULL, folder_en VARCHAR(255) NOT NULL, file_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, content_pt LONGTEXT NOT NULL, content_en LONGTEXT NOT NULL, date_pt VARCHAR(255) NOT NULL, date_en VARCHAR(255) NOT NULL, slug_pt VARCHAR(255) NOT NULL, slug_en VARCHAR(255) NOT NULL, registration_link VARCHAR(255) DEFAULT NULL, INDEX IDX_3BAE0AA73DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE example_entity (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, some_bool TINYINT(1) NOT NULL, some_list JSON DEFAULT NULL, some_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', sometime TIME DEFAULT NULL COMMENT \'(DC2Type:time_immutable)\', some_datetime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status INT NOT NULL, INDEX IDX_AFE7E950A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE example_entity_user (example_entity_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C6E00128BB2FD451 (example_entity_id), INDEX IDX_C6E00128A76ED395 (user_id), PRIMARY KEY(example_entity_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_banner (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, subtitle_pt VARCHAR(255) NOT NULL, subtitle_en VARCHAR(255) NOT NULL, button_text_pt VARCHAR(255) DEFAULT NULL, button_text_en VARCHAR(255) DEFAULT NULL, button_link VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, position INT DEFAULT 0 NOT NULL, INDEX IDX_9F99D15A3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_opportunity (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, status_pt VARCHAR(255) NOT NULL, status_en VARCHAR(255) NOT NULL, summary_pt VARCHAR(255) NOT NULL, summary_en VARCHAR(255) NOT NULL, content_pt LONGTEXT NOT NULL, content_en LONGTEXT NOT NULL, slug_pt VARCHAR(255) NOT NULL, slug_en VARCHAR(255) NOT NULL, pdf_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E0E5D89E3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, summary_pt VARCHAR(255) NOT NULL, summary_en VARCHAR(255) NOT NULL, content_pt LONGTEXT NOT NULL, content_en LONGTEXT NOT NULL, slug_pt VARCHAR(255) NOT NULL, slug_en VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', youtube_video_code VARCHAR(255) DEFAULT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, image_alt VARCHAR(255) DEFAULT NULL, canonical_url VARCHAR(255) DEFAULT NULL, is_no_index TINYINT(1) DEFAULT 0 NOT NULL, status VARCHAR(255) DEFAULT NULL, highlighted TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_1DD399503DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_content (id INT AUTO_INCREMENT NOT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, slug_pt VARCHAR(255) NOT NULL, slug_en VARCHAR(255) NOT NULL, content_pt LONGTEXT NOT NULL, content_en LONGTEXT NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, logo_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, position INT DEFAULT 0 NOT NULL, url VARCHAR(255) DEFAULT NULL, new_tab TINYINT(1) DEFAULT 1 NOT NULL, icon_class VARCHAR(255) DEFAULT \'fa-solid fa-building-columns\', region VARCHAR(10) DEFAULT \'BR\' NOT NULL, INDEX IDX_312B3E16F98F144A (logo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, pesquisador_id INT DEFAULT NULL, nome_pt VARCHAR(255) NOT NULL, nome_en VARCHAR(255) NOT NULL, objetivo_pt VARCHAR(255) NOT NULL, objetivo_en VARCHAR(255) NOT NULL, descricao_pt LONGTEXT NOT NULL, descricao_en LONGTEXT NOT NULL, modulo_pt VARCHAR(255) NOT NULL, modulo_en VARCHAR(255) NOT NULL, slug_pt VARCHAR(255) NOT NULL, slug_en VARCHAR(255) NOT NULL, INDEX IDX_2FB3D0EE7BFF0C1C (pesquisador_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_document (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, year INT DEFAULT NULL, researcher VARCHAR(255) DEFAULT NULL, doi VARCHAR(255) DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E52701AD166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE researcher (id INT AUTO_INCREMENT NOT NULL, foto_id INT DEFAULT NULL, nome VARCHAR(255) NOT NULL, area_pt VARCHAR(255) NOT NULL, area_en VARCHAR(255) NOT NULL, lattes VARCHAR(255) DEFAULT NULL, position INT DEFAULT 0 NOT NULL, curriculo_pt LONGTEXT DEFAULT NULL, curriculo_en LONGTEXT DEFAULT NULL, linkedin VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, INDEX IDX_75FF2EDE7ABFA656 (foto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_network (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, icon_class VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, name VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, reset_password_token VARCHAR(100) DEFAULT NULL, reset_password_expires_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, UNIQUE INDEX UNIQ_8D93D649452C9EC5 (reset_password_token), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE youtube_media (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, youtube_id VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA73DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE example_entity ADD CONSTRAINT FK_AFE7E950A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE example_entity_user ADD CONSTRAINT FK_C6E00128BB2FD451 FOREIGN KEY (example_entity_id) REFERENCES example_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE example_entity_user ADD CONSTRAINT FK_C6E00128A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE home_banner ADD CONSTRAINT FK_9F99D15A3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE job_opportunity ADD CONSTRAINT FK_E0E5D89E3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD399503DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16F98F144A FOREIGN KEY (logo_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE7BFF0C1C FOREIGN KEY (pesquisador_id) REFERENCES researcher (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project_document ADD CONSTRAINT FK_E52701AD166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE researcher ADD CONSTRAINT FK_75FF2EDE7ABFA656 FOREIGN KEY (foto_id) REFERENCES image (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA73DA5256D');
        $this->addSql('ALTER TABLE example_entity DROP FOREIGN KEY FK_AFE7E950A76ED395');
        $this->addSql('ALTER TABLE example_entity_user DROP FOREIGN KEY FK_C6E00128BB2FD451');
        $this->addSql('ALTER TABLE example_entity_user DROP FOREIGN KEY FK_C6E00128A76ED395');
        $this->addSql('ALTER TABLE home_banner DROP FOREIGN KEY FK_9F99D15A3DA5256D');
        $this->addSql('ALTER TABLE job_opportunity DROP FOREIGN KEY FK_E0E5D89E3DA5256D');
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD399503DA5256D');
        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E16F98F144A');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE7BFF0C1C');
        $this->addSql('ALTER TABLE project_document DROP FOREIGN KEY FK_E52701AD166D1F9C');
        $this->addSql('ALTER TABLE researcher DROP FOREIGN KEY FK_75FF2EDE7ABFA656');
        $this->addSql('DROP TABLE clipping');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE example_entity');
        $this->addSql('DROP TABLE example_entity_user');
        $this->addSql('DROP TABLE home_banner');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE job_opportunity');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE page_content');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_document');
        $this->addSql('DROP TABLE researcher');
        $this->addSql('DROP TABLE social_network');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE youtube_media');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
