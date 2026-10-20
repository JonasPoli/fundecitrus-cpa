<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261020120000 extends AbstractMigration
{
    private const IMAGE_SOURCE = 'public/images/sobre.jpg';
    private const IMAGE_NAME = 'quem-somos.jpg';

    private const CONTENT_PT = <<<'HTML'
        <p>O <strong>CPA Citros</strong> é um centro de pesquisa criado por meio de uma parceria estratégica entre a Esalq/USP, o Fundecitrus e a FAPESP, com o propósito de desenvolver soluções científicas e tecnológicas para os principais desafios da citricultura brasileira. A iniciativa reúne especialistas de diversas instituições e nacionalidades, consolidando a maior rede de inteligência já formada para o enfrentamento do HLB, a doença mais destrutiva dos citros no mundo.</p><p>Coordenado pela Esalq/USP, o CPA Citros é uma rede de laboratórios de pesquisa sem uma sede física, que integra pesquisadores de diferentes áreas — como fitotecnia, fitopatologia, melhoramento genético, biotecnologia, microbiologia, entomologia e ciência de dados —, promovendo uma atuação colaborativa e contínua. Seu objetivo é acelerar descobertas, gerar inovação aplicada ao campo e fortalecer a sustentabilidade da cadeia citrícola.</p><p>Atualmente, a principal atuação do centro está voltada à investigação da interação entre planta, <em>Candidatus</em> Liberibacter asiaticus e <em>Diaphorina citri</em>, no desenvolvimento de estratégias de manejo mais eficazes para combate ao HLB e na mitigação dos danos causados pela doença. Além disso, promove a difusão de conhecimento e a transferência de tecnologia para o setor, contribuindo para manter a citricultura brasileira competitiva e preparada para desafios futuros.</p><p>Com investimentos de longo prazo e uma rede internacional de colaboração, o CPA Citros representa um marco na pesquisa aplicada ao agronegócio, reforçando o compromisso do Brasil com a inovação, a sustentabilidade e a liderança mundial na produção de citros e derivados.</p>
        HTML;

    private const CONTENT_EN = <<<'HTML'
        <p>CPA Citros is a research center created through a strategic partnership between Esalq/USP, Fundecitrus and FAPESP, with the purpose of developing scientific and technological solutions to the main challenges facing Brazilian citriculture. The initiative brings together specialists from several institutions and nationalities, consolidating the largest intelligence network ever formed to tackle HLB, the most destructive citrus disease in the world.</p><p>Coordinated by Esalq/USP, CPA Citros is a network of research laboratories without a physical headquarters, integrating researchers from different fields — such as crop science, plant pathology, plant breeding, biotechnology, microbiology, entomology and data science — in a collaborative and continuous effort. Its goal is to accelerate discoveries, generate innovation applied to the field and strengthen the sustainability of the citrus chain.</p><p>Currently, the center's main focus is on investigating the interaction between the plant, <em>Candidatus</em> Liberibacter asiaticus and <em>Diaphorina citri</em>, on developing more effective management strategies to fight HLB and on mitigating the damage caused by the disease. It also promotes knowledge dissemination and technology transfer to the sector, helping keep Brazilian citriculture competitive and prepared for future challenges.</p><p>With long-term investments and an international collaboration network, CPA Citros represents a milestone in applied agribusiness research, reinforcing Brazil's commitment to innovation, sustainability and world leadership in the production of citrus and citrus products.</p>
        HTML;

    public function getDescription(): string
    {
        return 'Destaque na home para eventos e oportunidades; seção "Quem somos" gerenciável (texto e imagem atuais)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event ADD highlighted TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE job_opportunity ADD highlighted TINYINT(1) DEFAULT 0 NOT NULL');

        $this->addSql('CREATE TABLE about_section (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, title_pt VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, content_pt LONGTEXT NOT NULL, content_en LONGTEXT NOT NULL, INDEX IDX_5A624DCE3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE about_section ADD CONSTRAINT FK_5A624DCE3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE SET NULL');

        $projectDir = dirname(__DIR__);
        $uploadsDir = $projectDir . '/public/uploads';
        $imageCopied = (is_dir($uploadsDir) || @mkdir($uploadsDir, 0775, true))
            && @copy($projectDir . '/' . self::IMAGE_SOURCE, $uploadsDir . '/' . self::IMAGE_NAME);
        $this->warnIf(!$imageCopied, 'Não foi possível copiar ' . self::IMAGE_SOURCE . ' para public/uploads: envie a imagem de "Quem somos" pelo admin.');

        if ($imageCopied) {
            $this->addSql('INSERT INTO image (image_name, updated_at) VALUES (?, NOW())', [self::IMAGE_NAME]);
        }

        $this->addSql(
            'INSERT INTO about_section (title_pt, title_en, content_pt, content_en, image_id) VALUES (?, ?, ?, ?, ' . ($imageCopied ? 'LAST_INSERT_ID()' : 'NULL') . ')',
            ['Quem somos', 'Who we are', trim(self::CONTENT_PT), trim(self::CONTENT_EN)]
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE about_section DROP FOREIGN KEY FK_5A624DCE3DA5256D');
        $this->addSql('DROP TABLE about_section');
        $this->addSql('DELETE FROM image WHERE image_name = ?', [self::IMAGE_NAME]);
        $this->addSql('ALTER TABLE job_opportunity DROP highlighted');
        $this->addSql('ALTER TABLE event DROP highlighted');
    }
}
