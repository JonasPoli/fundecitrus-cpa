<?php

namespace App\Command;

use App\Entity\Clipping;
use App\Entity\Document;
use App\Entity\ProjectDocument;
use App\Entity\YoutubeMedia;
use App\Entity\Event;
use App\Entity\HomeBanner;
use App\Entity\Image;
use App\Entity\JobOpportunity;
use App\Entity\News;
use App\Entity\PageContent;
use App\Entity\Partner;
use App\Entity\Project;
use App\Entity\Researcher;
use App\Entity\SocialNetwork;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:seed-data',
    description: 'Seeds the database with rich bilingual mock data and copy mockup images.'
)]
class SeedDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting Database Seeding for CPA Citros');

        // 1. Limpeza do Banco de Dados
        $io->section('Cleaning existing database tables...');
        $conn = $this->entityManager->getConnection();
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 0;');

        $tables = [
            'clipping', 'document', 'event', 'home_banner', 'image',
            'job_opportunity', 'news', 'page_content', 'partner',
            'project', 'researcher', 'user', 'project_document', 'youtube_media',
            'social_network', 'research_area', 'research_line', 'research_module',
            'news_image', 'event_registration'
        ];

        foreach ($tables as $table) {
            $conn->executeStatement("DELETE FROM `{$table}`");
            $conn->executeStatement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
        }

        $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 1;');
        $io->success('Database cleaned successfully!');

        // 2. Configurações de Origem e Destino de Mídias
        $layoutImagesDir = $this->projectDir . '/public/fundecitrus-cpa/public/images';
        $uploadDestinationDir = $this->projectDir . '/public/uploads';
        $docsDestinationDir = $this->projectDir . '/public/uploads/documents';

        if (!is_dir($uploadDestinationDir)) {
            mkdir($uploadDestinationDir, 0777, true);
        }
        if (!is_dir($docsDestinationDir)) {
            mkdir($docsDestinationDir, 0777, true);
        }

        // Helper para upload de imagens
        $uploadImage = function (string $filename, string $type = 'other') use ($layoutImagesDir, $uploadDestinationDir, $io): ?Image {
            $sourcePath = $layoutImagesDir . '/' . $filename;
            if (!file_exists($sourcePath)) {
                $io->warning("Source file not found: " . $sourcePath);
                return null;
            }

            $newFilename = md5(uniqid() . $filename) . '.' . pathinfo($filename, PATHINFO_EXTENSION);
            $destPath = $uploadDestinationDir . '/' . $newFilename;

            copy($sourcePath, $destPath);

            $image = new Image();
            $image->setImageName($newFilename);
            $image->setType($type);
            $this->entityManager->persist($image);

            return $image;
        };

        $uploadDocumentFile = function (string $filename) use ($docsDestinationDir): string {
            $ext = pathinfo($filename, PATHINFO_EXTENSION) ?: 'txt';
            $destFilename = md5(uniqid() . $filename) . '.' . $ext;
            $destPath = $docsDestinationDir . '/' . $destFilename;
            file_put_contents($destPath, "Conteúdo científico simulado para o arquivo: " . $filename);
            return $destFilename;
        };

        // 3. Injeção de Usuários (Admin e Pesquisador)
        $io->section('Seeding Users...');
        
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@fundecitrus.com.br');
        $admin->setName('Administrador CPA');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'a'));
        $this->entityManager->persist($admin);

        $pesquisadorUser = new User();
        $pesquisadorUser->setUsername('pesquisador');
        $pesquisadorUser->setEmail('pesquisador@fundecitrus.com.br');
        $pesquisadorUser->setName('Dr. Marcos Antonio Machado');
        $pesquisadorUser->setRoles(['ROLE_USER']);
        $pesquisadorUser->setPassword($this->passwordHasher->hashPassword($pesquisadorUser, 'pesquisador123'));
        $this->entityManager->persist($pesquisadorUser);

        // 4. Injeção de Banners da Home
        $io->section('Seeding HomeBanners...');
        
        $b1 = new HomeBanner();
        $b1->setTitlePt('CPA Citros — Centro de Pesquisa Científica');
        $b1->setTitleEn('CPA Citros — Scientific Research Center');
        $b1->setSubtitlePt('Rede de inteligência e controle contra o HLB no mundo');
        $b1->setSubtitleEn('Global intelligence and control network against HLB');
        $b1->setButtonTextPt('Conhecer o Centro');
        $b1->setButtonTextEn('Explore the Center');
        $b1->setButtonLink('/pt/sobre');
        $b1->setIsActive(true);
        $b1->setPosition(1);
        $img1 = $uploadImage('banner1.jpg', 'banner');
        if ($img1) $b1->setImage($img1);
        $this->entityManager->persist($b1);

        $b2 = new HomeBanner();
        $b2->setTitlePt('Inovação para a Citricultura');
        $b2->setTitleEn('Innovation for Citriculture');
        $b2->setSubtitlePt('Pesquisadores unidos no combate ao Greening');
        $b2->setSubtitleEn('Researchers united in the fight against Greening');
        $b2->setButtonTextPt('Nossas Linhas de Pesquisa');
        $b2->setButtonTextEn('Research Fields');
        $b2->setButtonLink('/pt/pesquisa');
        $b2->setIsActive(true);
        $b2->setPosition(2);
        $img2 = $uploadImage('banner2.jpg', 'banner');
        if ($img2) $b2->setImage($img2);
        $this->entityManager->persist($b2);

        // 5. Injeção de Pesquisadores
        $io->section('Seeding Researchers...');
        
        $p1 = new Researcher();
        $p1->setNome('Dr. Marcos Antonio Machado');
        $p1->setAreaPt('Manejo e controle do HLB');
        $p1->setAreaEn('HLB management and control');
        $p1->setLattes('http://lattes.cnpq.br/3820358203859235');
        $p1->setPosition(1);
        $f1 = $uploadImage('foto_pesquisador.png', 'researcher');
        if ($f1) $p1->setFoto($f1);
        $this->entityManager->persist($p1);

        $p2 = new Researcher();
        $p2->setNome('Dra. Maria Julia da Silva');
        $p2->setAreaPt('Interação Genômica');
        $p2->setAreaEn('Genomic Interaction');
        $p2->setLattes('http://lattes.cnpq.br/1293812039120391');
        $p2->setPosition(2);
        $f2 = $uploadImage('foto_pesquisador.png', 'researcher');
        if ($f2) $p2->setFoto($f2);
        $this->entityManager->persist($p2);

        $p3 = new Researcher();
        $p3->setNome('Dr. Carlos Alberto Costa');
        $p3->setAreaPt('Mitigação e sustentabilidade');
        $p3->setAreaEn('Mitigation and sustainability');
        $p3->setLattes('http://lattes.cnpq.br/4829381923891823');
        $p3->setPosition(3);
        $f3 = $uploadImage('foto_pesquisador.png', 'researcher');
        if ($f3) $p3->setFoto($f3);
        $this->entityManager->persist($p3);

        // 6. Injeção de Projetos
        $io->section('Seeding Projects...');
        
        $proj1 = new Project();
        $proj1->setNomePt('Manejo Integrado de Greening no Cinturão Citrícola');
        $proj1->setNomeEn('Integrated Greening Management in the Citrus Belt');
        $proj1->setObjetivoPt('Avaliar novos métodos de controle regional de vetores.');
        $proj1->setObjetivoEn('Evaluate new regional vector control methods.');
        $proj1->setDescricaoPt('<p>O projeto investiga o controle populacional de <i>Diaphorina citri</i> através de manejos integrados, pulverizações conjuntas e coordenação regional.</p>');
        $proj1->setDescricaoEn('<p>The project investigates the population control of <i>Diaphorina citri</i> through integrated management, coordinated sprays and regional synchronization.</p>');
        $proj1->setSlugPt('manejo-integrado-greening');
        $proj1->setSlugEn('integrated-greening-management');
        $proj1->setPesquisador($p1);
        $this->entityManager->persist($proj1);

        $proj2 = new Project();
        $proj2->setNomePt('Identificação de Marcadores Genéticos de Resistência');
        $proj2->setNomeEn('Identification of Genetic Resistance Markers');
        $proj2->setObjetivoPt('Mapear genes de suscetibilidade e resistência ao HLB.');
        $proj2->setObjetivoEn('Map susceptibility and resistance genes to HLB.');
        $proj2->setDescricaoPt('<p>Estudo focado no sequenciamento e identificação de genes específicos em citros tolerantes ao greening.</p>');
        $proj2->setDescricaoEn('<p>Study focused on sequencing and identifying specific genes in HLB-tolerant citrus cultivars.</p>');
        $proj2->setSlugPt('identificacao-marcadores-geneticos');
        $proj2->setSlugEn('identification-genetic-markers');
        $proj2->setPesquisador($p2);
        $this->entityManager->persist($proj2);

        $proj3 = new Project();
        $proj3->setNomePt('Avaliação de Modelos de Sustentabilidade Econômica');
        $proj3->setNomeEn('Evaluation of Economic Sustainability Models');
        $proj3->setObjetivoPt('Calcular a taxa de retorno de propriedades com controle rígido.');
        $proj3->setObjetivoEn('Calculate return rate of orchards with rigorous control.');
        $proj3->setDescricaoPt('<p>Análise de custos operacionais do manejo do greening versus as perdas causadas pelo abandono ou ineficiência de controle.</p>');
        $proj3->setDescricaoEn('<p>Cost analysis of greening management options vs losses incurred by inefficient or lack of vector control.</p>');
        $proj3->setSlugPt('avaliacao-modelos-sustentabilidade');
        $proj3->setSlugEn('evaluation-sustainability-models');
        $proj3->setPesquisador($p3);
        $this->entityManager->persist($proj3);

        // 7. Injeção de Notícias
        $io->section('Seeding News...');
        
        $n1 = new News();
        $n1->setTitlePt('CPA Citros anuncia novas frentes no combate ao Greening');
        $n1->setTitleEn('CPA Citros announces new fronts in the fight against Greening');
        $n1->setSummaryPt('Encontro internacional com cientistas discute manejos inovadores e genômica.');
        $n1->setSummaryEn('International meeting with scientists discusses innovative managements and genomics.');
        $n1->setContentPt('<p>O CPA Citros reuniu os principais pesquisadores do mundo para debater novas técnicas de interrupção reprodutiva do vetor e identificação de plantas tolerantes.</p>');
        $n1->setContentEn('<p>CPA Citros gathered top global researchers to discuss new techniques for vector reproductive disruption and identification of tolerant citrus varieties.</p>');
        $n1->setSlugPt('cpa-citros-novas-frentes-greening');
        $n1->setSlugEn('cpa-citros-new-fronts-greening');
        $n1->setDate(new \DateTimeImmutable());
        $imn1 = $uploadImage('news1.jpg', 'news');
        if ($imn1) $n1->setImage($imn1);
        $this->entityManager->persist($n1);

        $n2 = new News();
        $n2->setTitlePt('Resultados parciais indicam eficácia de manejos biológicos');
        $n2->setTitleEn('Partial results indicate effectiveness of biological controls');
        $n2->setSummaryPt('Estudos em fazendas piloto mostram redução de até 40% na população de vetores.');
        $n2->setSummaryEn('Pilot farm studies show up to a 40% reduction in vector population.');
        $n2->setContentPt('<p>Experimentos liderados por pesquisadores do CPA mostram que a introdução de inimigos naturais em áreas externas diminuiu significativamente o fluxo de psilídeos para os pomares comerciais.</p>');
        $n2->setContentEn('<p>Experiments led by CPA researchers show that introducing natural enemies in external areas significantly reduced psyllid pressure in commercial groves.</p>');
        $n2->setSlugPt('resultados-parciais-manejos-biologicos');
        $n2->setSlugEn('partial-results-biological-control');
        $n2->setDate(new \DateTimeImmutable('-5 days'));
        $imn2 = $uploadImage('news2.jpg', 'news');
        if ($imn2) $n2->setImage($imn2);
        $this->entityManager->persist($n2);

        $n3 = new News();
        $n3->setTitlePt('Parcerias institucionais expandem estudos genéticos');
        $n3->setTitleEn('Institutional partnerships expand genetic studies');
        $n3->setSummaryPt('CPA assina cooperação científica internacional com centros europeus.');
        $n3->setSummaryEn('CPA signs international scientific cooperation agreement with European centers.');
        $n3->setContentPt('<p>A nova parceria visa acelerar a análise genômica de espécies nativas com indicativos de imunidade natural contra bactérias associadas ao greening.</p>');
        $n3->setContentEn('<p>The new partnership aims to accelerate genomic profiling of native citrus relatives showing signs of natural immunity to greening associated bacteria.</p>');
        $n3->setSlugPt('parcerias-institucionais-estudos-geneticos');
        $n3->setSlugEn('partnerships-expand-genetic-studies');
        $n3->setDate(new \DateTimeImmutable('-10 days'));
        $imn3 = $uploadImage('news3.jpg', 'news');
        if ($imn3) $n3->setImage($imn3);
        $this->entityManager->persist($n3);

        // 8. Injeção de Eventos
        $io->section('Seeding Events...');
        
        $ev1 = new Event();
        $ev1->setTitlePt('I Simpósio Internacional de HLB');
        $ev1->setTitleEn('I International HLB Symposium');
        $ev1->setContentPt('<p>Primeiro evento de grande porte promovido pelo CPA, focado na difusão de metodologias aplicadas e cooperação transnacional.</p>');
        $ev1->setContentEn('<p>First major event promoted by CPA, focusing on applied methodologies dissemination and transnational cooperation.</p>');
        $ev1->setDatePt('15 a 18 de Novembro de 2026');
        $ev1->setDateEn('November 15-18, 2026');
        $ev1->setSlugPt('simposio-internacional-hlb');
        $ev1->setSlugEn('international-hlb-symposium');
        $imev1 = $uploadImage('simposio_hlb_banner.png', 'event');
        if ($imev1) $ev1->setImage($imev1);
        $this->entityManager->persist($ev1);

        $ev2 = new Event();
        $ev2->setTitlePt('Workshop de Controle Biológico do Vetor');
        $ev2->setTitleEn('Vector Biological Control Workshop');
        $ev2->setContentPt('<p>Workshop prático ensinando produtores a multiplicar e soltar a vespa tamarixia em arredores de propriedades.</p>');
        $ev2->setContentEn('<p>Practical workshop training growers to rear and release Tamarixia radiata parasitoids around commercial groves.</p>');
        $ev2->setDatePt('05 de Dezembro de 2026');
        $ev2->setDateEn('December 5, 2026');
        $ev2->setSlugPt('workshop-controle-biologico-vetor');
        $ev2->setSlugEn('vector-biological-control-workshop');
        $imev2 = $uploadImage('workshop_biologico_banner.png', 'event');
        if ($imev2) $ev2->setImage($imev2);
        $this->entityManager->persist($ev2);

        // 9. Injeção de Vagas
        $io->section('Seeding JobOpportunities...');
        
        $job1 = new JobOpportunity();
        $job1->setTitlePt('Bolsista de Pós-Doutorado em Genômica de Plantas');
        $job1->setTitleEn('Post-Doctoral Fellowship in Plant Genomics');
        $job1->setStatusPt('Aberto');
        $job1->setStatusEn('Open');
        $job1->setSummaryPt('Oportunidade para doutores com experiência em sequenciamento genético e bioinformática.');
        $job1->setSummaryEn('Opportunity for PhDs with experience in gene sequencing and bioinformatics.');
        $job1->setContentPt('<p>O selecionado atuará na análise filogenética e perfil comparativo de variedades cítricas sob estresse biótico.</p>');
        $job1->setContentEn('<p>The selected candidate will work on phylogenetic analysis and comparative profiling of citrus varieties under biotic stress.</p>');
        $job1->setSlugPt('pos-doc-genomica-plantas');
        $job1->setSlugEn('post-doc-plant-genomics');
        $imj1 = $uploadImage('vagas_banner.png', 'job');
        if ($imj1) $job1->setImage($imj1);
        $job1->setPdfName($uploadDocumentFile('edital-pos-doc.pdf'));
        $this->entityManager->persist($job1);

        $job2 = new JobOpportunity();
        $job2->setTitlePt('Pesquisador Pleno em Fitopatologia');
        $job2->setTitleEn('Associate Researcher in Plant Pathology');
        $job2->setStatusPt('Encerrado');
        $job2->setStatusEn('Closed');
        $job2->setSummaryPt('Cargo permanente de pesquisa focada em testes de resistência molecular.');
        $job2->setSummaryEn('Permanent research position focusing on molecular resistance testing.');
        $job2->setContentPt('<p>Cargo permanente para gerenciar ensaios de campo e estufa contra isolados bacterianos.</p>');
        $job2->setContentEn('<p>Permanent position to manage greenhouse and field trials against bacterial isolates.</p>');
        $job2->setSlugPt('pesquisador-pleno-fitopatologia');
        $job2->setSlugEn('associate-researcher-plant-pathology');
        $imj2 = $uploadImage('vagas_banner.png', 'job');
        if ($imj2) $job2->setImage($imj2);
        $this->entityManager->persist($job2);

        // 10. Injeção de Clippings
        $io->section('Seeding Clippings...');
        
        $clip1 = new Clipping();
        $clip1->setVeiculo('Globo Rural');
        $clip1->setTitlePt('Novo centro científico unifica pesquisas contra o HLB');
        $clip1->setTitleEn('New scientific center unifies research against HLB');
        $clip1->setLink('https://globorural.globo.com');
        $this->entityManager->persist($clip1);

        $clip2 = new Clipping();
        $clip2->setVeiculo('Canal Rural');
        $clip2->setTitlePt('CPA Citros inicia testes em campo com novas espécies tolerantes');
        $clip2->setTitleEn('CPA Citros initiates field trials with new tolerant species');
        $clip2->setLink('https://www.canalrural.com.br');
        $this->entityManager->persist($clip2);

        // 11. Injeção de Parceiros
        $io->section('Seeding Partners...');
        
        $part1 = new Partner();
        $part1->setName('Fundecitrus');
        $part1->setPosition(1);
        $part1->setUrl('https://www.fundecitrus.com.br');
        $part1->setNewTab(true);
        $part1->setCountry('BR');
        $lg1 = $uploadImage('logo.png', 'partner');
        if ($lg1) $part1->setLogo($lg1);
        $this->entityManager->persist($part1);

        $part2 = new Partner();
        $part2->setName('FEALQ');
        $part2->setPosition(2);
        $part2->setUrl('https://fealq.org.br');
        $part2->setNewTab(true);
        $part2->setCountry('BR');
        $lg2 = $uploadImage('logo.png', 'partner');
        if ($lg2) $part2->setLogo($lg2);
        $this->entityManager->persist($part2);

        $part3 = new Partner();
        $part3->setName('USP');
        $part3->setPosition(3);
        $part3->setUrl('https://www.usp.br');
        $part3->setNewTab(true);
        $part3->setCountry('BR');
        $lg3 = $uploadImage('logo.png', 'partner');
        if ($lg3) $part3->setLogo($lg3);
        $this->entityManager->persist($part3);

        $brInst = [
            'Centro de Energia Nuclear na Agricultura - Cena - USP',
            'Faculdade de Zootecnia e Engenharia de Alimentos - FZEA - USP',
            'Universidade Federal de São Carlos - UFSCar',
            'Universidade Estadual de Campinas - Unicamp',
            'Instituto Biológico de São Paulo',
            'Universidade Estadual Paulista - Unesp',
            'Instituto Agronômico de Campinas - IAC',
            'Empresa Brasileira de Pesquisa Agropecuária - Embrapa'
        ];
        $pos = 4;
        foreach ($brInst as $name) {
            $p = new Partner();
            $p->setName($name);
            $p->setPosition($pos++);
            $p->setCountry('BR');
            $p->setIconClass('fa-solid fa-building-columns');
            $p->setUrl('https://google.com');
            $this->entityManager->persist($p);
        }

        $intInst = [
            'Centro de Cooperação Internacional - Cirad | França',
            'Conselho Superior de Pesquisas Científicas - CSIC | Espanha',
            'Instituto Andaluz - Ifapa | Espanha',
            'Universidade da Flórida | EUA',
            'Universidade da Califórnia | EUA',
            'Departamento de Queensland | Austrália',
            'Universidade de Durham | Inglaterra',
            'Universidade de Cambridge | Inglaterra',
            'Universidade de Warwick | Inglaterra',
            'Universidade do Algarve | Portugal'
        ];
        foreach ($intInst as $name) {
            $p = new Partner();
            $p->setName($name);
            $p->setPosition($pos++);
            $p->setCountry('US');
            $p->setIconClass('fa-solid fa-earth-americas');
            $p->setUrl('https://google.com');
            $this->entityManager->persist($p);
        }

        // 12. Injeção de Páginas Institucionais (Quill)
        $io->section('Seeding PageContent...');
        
        $pg1 = new PageContent();
        $pg1->setTitlePt('Política de Privacidade');
        $pg1->setTitleEn('Privacy Policy');
        $pg1->setSlugPt('politica-de-privacidade');
        $pg1->setSlugEn('privacy-policy');
        $pg1->setContentPt('<h3>1. Coleta de Dados</h3><p>O CPA Citros respeita sua privacidade e coleta apenas os dados de identificação necessários para a autenticação e acesso à Área Restrita de pesquisadores.</p>');
        $pg1->setContentEn('<h3>1. Data Collection</h3><p>CPA Citros respects your privacy and only collects identity data required for authentication and access to the Researchers Restricted Area.</p>');
        $pg1->setIsActive(true);
        $this->entityManager->persist($pg1);

        $pg2 = new PageContent();
        $pg2->setTitlePt('Termos de Uso');
        $pg2->setTitleEn('Terms of Use');
        $pg2->setSlugPt('termos-de-uso');
        $pg2->setSlugEn('terms-of-use');
        $pg2->setContentPt('<h3>1. Conduta do Usuário</h3><p>O acesso e download de arquivos da Área Restrita destinam-se exclusivamente para estudos e fins colaborativos científicos da rede CPA Citros.</p>');
        $pg2->setContentEn('<h3>1. User Conduct</h3><p>Access and file downloads from the Restricted Area are exclusively intended for scientific collaboration and research purposes of the CPA Citros network.</p>');
        $pg2->setIsActive(true);
        $this->entityManager->persist($pg2);

        // 13. Injeção de Documentos Restritos
        $io->section('Seeding Documents for Restricted Area...');
        
        $doc1 = new Document();
        $doc1->setTitlePt('Relatório Anual de Avanços CPA 2025');
        $doc1->setTitleEn('CPA Annual Progress Report 2025');
        $doc1->setFolderPt('Relatórios de Pesquisa');
        $doc1->setFolderEn('Research Reports');
        $doc1->setFileName($uploadDocumentFile('relatorio-2025.txt'));
        $this->entityManager->persist($doc1);

        $doc2 = new Document();
        $doc2->setTitlePt('Dados Brutos de Captura do Psilídeo Diaphorina');
        $doc2->setTitleEn('Raw Capture Data of Diaphorina Psyllid');
        $doc2->setFolderPt('Dados Primários');
        $doc2->setFolderEn('Primary Data');
        $doc2->setFileName($uploadDocumentFile('dados-captura.txt'));
        $this->entityManager->persist($doc2);

        $doc3 = new Document();
        $doc3->setTitlePt('Anais do I Simpósio Internacional de Greening');
        $doc3->setTitleEn('Proceedings of the I International Greening Symposium');
        $doc3->setFolderPt('Anais de Eventos');
        $doc3->setFolderEn('Event Proceedings');
        $doc3->setFileName($uploadDocumentFile('anais-simposio-1.pdf'));
        $this->entityManager->persist($doc3);

        $doc4 = new Document();
        $doc4->setTitlePt('Anais do II Simpósio Internacional de Greening');
        $doc4->setTitleEn('Proceedings of the II International Greening Symposium');
        $doc4->setFolderPt('Anais de Eventos');
        $doc4->setFolderEn('Event Proceedings');
        $doc4->setFileName($uploadDocumentFile('anais-simposio-2.pdf'));
        $this->entityManager->persist($doc4);

        // 14. Injeção de Redes Sociais
        $io->section('Seeding Social Networks...');
        
        $sn1 = new SocialNetwork();
        $sn1->setName('Instagram');
        $sn1->setUrl('https://instagram.com');
        $sn1->setIconClass('fa-brands fa-instagram');
        $sn1->setIsActive(true);
        $sn1->setPosition(1);
        $this->entityManager->persist($sn1);

        $sn2 = new SocialNetwork();
        $sn2->setName('Facebook');
        $sn2->setUrl('https://facebook.com');
        $sn2->setIconClass('fa-brands fa-facebook-f');
        $sn2->setIsActive(true);
        $sn2->setPosition(2);
        $this->entityManager->persist($sn2);

        $sn3 = new SocialNetwork();
        $sn3->setName('LinkedIn');
        $sn3->setUrl('https://linkedin.com');
        $sn3->setIconClass('fa-brands fa-linkedin-in');
        $sn3->setIsActive(true);
        $sn3->setPosition(3);
        $this->entityManager->persist($sn3);

        $sn4 = new SocialNetwork();
        $sn4->setName('YouTube');
        $sn4->setUrl('https://youtube.com');
        $sn4->setIconClass('fa-brands fa-youtube');
        $sn4->setIsActive(true);
        $sn4->setPosition(4);
        $this->entityManager->persist($sn4);

        // 15. Injeção de Documentos de Projetos
        $io->section('Seeding Project Documents...');
        
        $pdoc1 = new ProjectDocument();
        $pdoc1->setProject($proj1);
        $pdoc1->setTitle('Mapeamento epidemiológico do Greening no Estado de São Paulo');
        $pdoc1->setType('artigo');
        $pdoc1->setYear(2025);
        $pdoc1->setResearcher('Dr. Marcos Antônio');
        $pdoc1->setDoi('https://doi.org/10.1016/j.jip.2025.108251');
        $this->entityManager->persist($pdoc1);

        $pdoc2 = new ProjectDocument();
        $pdoc2->setProject($proj2);
        $pdoc2->setTitle('Genomic signatures of Diaphorina citri feeding on citrus plants');
        $pdoc2->setType('abstract');
        $pdoc2->setYear(2026);
        $pdoc2->setResearcher('Dra. Aline Souza');
        $pdoc2->setDoi('https://doi.org/10.1016/j.plantsci.2026.111812');
        $this->entityManager->persist($pdoc2);

        $pdoc3 = new ProjectDocument();
        $pdoc3->setProject($proj3);
        $pdoc3->setTitle('Estudo econômico comparativo de estratégias de manejo de Greening');
        $pdoc3->setType('dissertação');
        $pdoc3->setYear(2024);
        $pdoc3->setResearcher('Rodrigo Silva');
        $pdoc3->setFileName($uploadDocumentFile('artigo-2024.pdf'));
        $this->entityManager->persist($pdoc3);

        // 16. Injeção de YoutubeMedia
        $io->section('Seeding Youtube Media (Videos & Podcasts)...');

        $ym1 = new YoutubeMedia();
        $ym1->setType('video');
        $ym1->setTitle('Manejo Integrado de Greening');
        $ym1->setDescription('Palestra sobre novas estratégias de combate e controle do vetor do Greening.');
        $ym1->setYoutubeId('dQw4w9WgXcQ');
        $ym1->setPosition(1);
        $this->entityManager->persist($ym1);

        $ym2 = new YoutubeMedia();
        $ym2->setType('podcast');
        $ym2->setTitle('Ciência e Agricultura no Combate ao HLB');
        $ym2->setDescription('Entrevista exclusiva com coordenadores do CPA sobre as pesquisas genômicas.');
        $ym2->setYoutubeId('dQw4w9WgXcQ');
        $ym2->setPosition(2);
        $this->entityManager->persist($ym2);

        // Flush
        $this->entityManager->flush();
        $io->success('Database Seeding finished successfully!');

        return Command::SUCCESS;
    }
}
