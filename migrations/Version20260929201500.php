<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260929201500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Categorias e empresas (logos) do rodapé, gerenciáveis pelo admin';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE footer_category (id INT AUTO_INCREMENT NOT NULL, name_pt VARCHAR(120) NOT NULL, name_en VARCHAR(120) NOT NULL, logo_size VARCHAR(2) DEFAULT \'md\' NOT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE footer_company (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, logo_id INT DEFAULT NULL, name VARCHAR(150) NOT NULL, url VARCHAR(255) DEFAULT NULL, new_tab TINYINT(1) DEFAULT 1 NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, position INT DEFAULT 0 NOT NULL, INDEX IDX_2F54730712469DE2 (category_id), INDEX IDX_2F547307F98F144A (logo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE footer_company ADD CONSTRAINT FK_2F54730712469DE2 FOREIGN KEY (category_id) REFERENCES footer_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE footer_company ADD CONSTRAINT FK_2F547307F98F144A FOREIGN KEY (logo_id) REFERENCES image (id) ON DELETE SET NULL');

        $this->addSql("INSERT INTO footer_category (id, name_pt, name_en, logo_size, position) VALUES (1, 'Financiadores', 'Funding', 'md', 1), (2, 'Apoio', 'Support', 'sm', 2)");
        $this->addSql("INSERT INTO footer_company (category_id, name, url, new_tab, active, position) VALUES
            (1, 'Esalq/USP', 'https://www.esalq.usp.br', 1, 1, 1),
            (1, 'FAPESP', 'https://fapesp.br', 1, 1, 2),
            (1, 'Fundecitrus', 'https://www.fundecitrus.com.br', 1, 1, 3),
            (2, 'Fealq', 'https://fealq.org.br', 1, 1, 1)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE footer_company DROP FOREIGN KEY FK_2F54730712469DE2');
        $this->addSql('ALTER TABLE footer_company DROP FOREIGN KEY FK_2F547307F98F144A');
        $this->addSql('DROP TABLE footer_company');
        $this->addSql('DROP TABLE footer_category');
    }
}
