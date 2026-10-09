<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261009150000 extends AbstractMigration
{
    private const GOVERNANCE = [
        ['mantenedores', 'Fapesp', null, 'Fundação de Amparo à Pesquisa do Estado de São Paulo', 'São Paulo Research Foundation'],
        ['mantenedores', 'Fundecitrus', null, 'Fundo de Defesa da Citricultura', 'Fund for Citrus Protection'],
        ['coordenacao', 'Dra. Lilian Amorim', null, 'Diretora (Esalq-USP)', 'Director (Esalq-USP)'],
        ['coordenacao', 'Dr. Renato B. Bassanezi', null, 'Vice-Diretor (Fundecitrus)', 'Vice-Director (Fundecitrus)'],
        ['gestao_projetos', 'A. Juliano Ayres', null, 'Coordenador (Fundecitrus)', 'Coordinator (Fundecitrus)'],
        ['gestao_projetos', 'Patrícia Louvandini', null, 'Gestor', 'Manager'],
        ['comite_administrativo', 'Luciana T. Lima', null, 'Fundecitrus', null],
        ['comite_administrativo', 'Equipe Fealq', 'Fealq team', null, null],
        ['comite_executivo', 'Dra. Lilian Amorim', null, 'Diretora (Esalq-USP)', 'Director (Esalq-USP)'],
        ['comite_executivo', 'Dr. Renato B. Bassanezi', null, 'Vice-Diretor (Fundecitrus)', 'Vice-Director (Fundecitrus)'],
        ['comite_executivo', 'A. Juliano Ayres', null, 'Coord. Projetos (Fundecitrus)', 'Projects Coordinator (Fundecitrus)'],
        ['comite_executivo', 'Dr. Geraldo J. Silva Jr.', null, 'Coord. Educação e Difusão do Conhecimento (Fundecitrus)', 'Education and Knowledge Dissemination Coordinator (Fundecitrus)'],
        ['comite_executivo', 'Dra. Beatriz A. da Glória', null, 'Coord. Educação e Difusão do Conhecimento (Esalq-USP)', 'Education and Knowledge Dissemination Coordinator (Esalq-USP)'],
        ['comite_executivo', 'Ivaldo Sala', null, 'Coord. Transferência de Tecnologia (Fundecitrus)', 'Technology Transfer Coordinator (Fundecitrus)'],
        ['comite_executivo', 'Dr. Pedro T. Yamamoto', null, 'Coord. Transferência de Tecnologia (Esalq-USP)', 'Technology Transfer Coordinator (Esalq-USP)'],
        ['conselho_setor', 'Alexandre Tachibana', null, 'Cambuhy', null],
        ['conselho_setor', 'Anderson José Pletsch', null, 'Citrosuco', null],
        ['conselho_setor', 'André Luis Alves de Souza', null, 'JFCitrus', null],
        ['conselho_setor', 'Antonio Ricardo Violante', null, 'Cutrale', null],
        ['conselho_setor', 'Luiz Fernando Girotto', null, 'Faro Capital', null],
        ['conselho_setor', 'Mauricio Lemos Mendes da Silva', null, 'Cia Agrícola São Luiz do Pinhal', null],
        ['conselho_setor', 'Rene Sanches de Souza Lima', null, 'LDC', null],
        ['conselho_setor', 'Thiago Iost Antunes', null, 'Branco Peres', null],
        ['conselho_internacional', 'Dr. Ruy Caldas', null, 'Un. Brasília', 'University of Brasília'],
        ['conselho_internacional', 'Dr. José R. P. Parra', null, 'Esalq-USP', null],
        ['conselho_internacional', 'Dr. James H. Graham', null, 'UF - EUA', 'UF - USA'],
        ['conselho_internacional', 'Dr. Luis Navarro Lucas', null, 'IVIA - Espanha', 'IVIA - Spain'],
    ];

    public function getDescription(): string
    {
        return 'Ajustes out/2026: categoria da equipe, imagem nas linhas de pesquisa (sem grande área obrigatória) e itens da governança';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE researcher ADD category VARCHAR(30) DEFAULT \'pesquisador\' NOT NULL');

        $this->addSql('UPDATE research_line l INNER JOIN research_area a ON a.id = l.area_id SET l.position = a.position * 100 + l.position');
        $this->addSql('ALTER TABLE research_line DROP FOREIGN KEY FK_84A48426BD0F409C');
        $this->addSql('ALTER TABLE research_line ADD image_id INT DEFAULT NULL, CHANGE area_id area_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE research_line ADD CONSTRAINT FK_84A48426BD0F409C FOREIGN KEY (area_id) REFERENCES research_area (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE research_line ADD CONSTRAINT FK_84A484263DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_84A484263DA5256D ON research_line (image_id)');

        $this->addSql('CREATE TABLE governance_member (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, governance_group VARCHAR(30) NOT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) DEFAULT NULL, description_pt LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, position INT DEFAULT 0 NOT NULL, INDEX IDX_AD0A05863DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE governance_member ADD CONSTRAINT FK_AD0A05863DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');

        $positions = [];
        foreach (self::GOVERNANCE as [$group, $titlePt, $titleEn, $descriptionPt, $descriptionEn]) {
            $positions[$group] = ($positions[$group] ?? 0) + 1;
            $this->addSql(
                'INSERT INTO governance_member (governance_group, title_pt, title_en, description_pt, description_en, position) VALUES (?, ?, ?, ?, ?, ?)',
                [$group, $titlePt, $titleEn, $descriptionPt, $descriptionEn, $positions[$group]]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE governance_member DROP FOREIGN KEY FK_AD0A05863DA5256D');
        $this->addSql('DROP TABLE governance_member');

        $this->addSql('ALTER TABLE research_line DROP FOREIGN KEY FK_84A484263DA5256D');
        $this->addSql('ALTER TABLE research_line DROP FOREIGN KEY FK_84A48426BD0F409C');
        $this->addSql('DROP INDEX IDX_84A484263DA5256D ON research_line');
        $this->addSql('ALTER TABLE research_line DROP image_id, CHANGE area_id area_id INT NOT NULL');
        $this->addSql('ALTER TABLE research_line ADD CONSTRAINT FK_84A48426BD0F409C FOREIGN KEY (area_id) REFERENCES research_area (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE researcher DROP category');
    }
}
