<?php

namespace App\Command;

use App\Entity\HomeBanner;
use App\Entity\Image;
use App\Entity\Partner;
use App\Entity\Project;
use App\Entity\ResearchArea;
use App\Entity\ResearchLine;
use App\Entity\ResearchModule;
use App\Entity\Researcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:cpa:sync-conteudo',
    description: 'Sincroniza instituições parceiras, pesquisadores, linhas de pesquisa e banners com o conteúdo enviado pelo cliente (set/2026).',
)]
class SyncCpaContentCommand extends Command
{
    private const PLACEHOLDER_PROJECT_SLUGS = [
        'manejo-integrado-greening',
        'identificacao-marcadores-geneticos',
        'avaliacao-modelos-sustentabilidade',
    ];

    private const HONORIFICS = '/^(dr\.?|dra\.?|prof\.?|profa\.?|prof\.ª)\s+/iu';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%')] private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Mostra o que seria feito e desfaz tudo no final.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');
        $io->title('Sincronização de conteúdo CPA Citros' . ($dryRun ? ' (simulação)' : ''));

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $this->syncResearchStructure($io);
            $this->syncPartners($io);
            $this->syncResearchers($io);
            $this->removePlaceholderProjects($io);
            $this->syncBanners($io, $dryRun);
            $this->entityManager->flush();

            if ($dryRun) {
                $connection->rollBack();
                $io->warning('Simulação concluída: nenhuma alteração foi gravada.');
            } else {
                $connection->commit();
                $io->success('Conteúdo sincronizado.');
            }
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function syncResearchStructure(SymfonyStyle $io): void
    {
        $io->section('Grandes áreas, linhas e módulos');
        $areaRepository = $this->entityManager->getRepository(ResearchArea::class);
        $lineRepository = $this->entityManager->getRepository(ResearchLine::class);
        $moduleRepository = $this->entityManager->getRepository(ResearchModule::class);

        foreach (self::researchStructure() as $areaPosition => $areaData) {
            $area = $areaRepository->findOneBy(['namePt' => $areaData['pt']]) ?? new ResearchArea();
            $area->setNamePt($areaData['pt'])->setNameEn($areaData['en'])->setPosition($areaPosition + 1);
            $this->entityManager->persist($area);
            $io->writeln(sprintf('<info>%s</info>', $areaData['pt']));

            foreach ($areaData['lines'] as $linePosition => $lineData) {
                $line = $lineRepository->findOneBy(['namePt' => $lineData['pt']]) ?? new ResearchLine();
                $line->setArea($area)
                    ->setNamePt($lineData['pt'])
                    ->setNameEn($lineData['en'])
                    ->setDescriptionPt($lineData['descPt'])
                    ->setDescriptionEn($lineData['descEn'])
                    ->setPosition($linePosition + 1);
                $this->entityManager->persist($line);
                $io->writeln('  • ' . $lineData['pt']);

                foreach ($lineData['modules'] as $modulePosition => [$pt, $en]) {
                    $module = $line->getId() ? $moduleRepository->findOneBy(['line' => $line, 'namePt' => $pt]) : null;
                    if (!$module) {
                        $module = new ResearchModule();
                        $line->addModule($module);
                    }
                    $module->setNamePt($pt)->setNameEn($en)->setPosition($modulePosition + 1);
                    $this->entityManager->persist($module);
                    $io->writeln('      – ' . $pt);
                }
            }
        }
    }

    private function syncPartners(SymfonyStyle $io): void
    {
        $io->section('Instituições parceiras');
        $repository = $this->entityManager->getRepository(Partner::class);
        $keep = [];

        foreach (self::partners() as $position => [$acronym, $name, $url, $country, $city, $lat, $lng]) {
            $partner = $repository->findOneBy(['acronym' => $acronym]) ?? new Partner();
            $partner->setAcronym($acronym)
                ->setName($name)
                ->setUrl($url)
                ->setNewTab(true)
                ->setCountry($country)
                ->setCity($city)
                ->setLatitude((string) $lat)
                ->setLongitude((string) $lng)
                ->setIconClass($country === 'BR' ? 'fa-solid fa-building-columns' : 'fa-solid fa-earth-americas')
                ->setPosition($position + 1);
            $this->entityManager->persist($partner);
            $keep[] = $partner;
        }

        $removed = 0;
        foreach ($repository->findAll() as $partner) {
            if (!in_array($partner, $keep, true)) {
                $this->entityManager->remove($partner);
                $removed++;
            }
        }

        $io->writeln(sprintf('%d instituições cadastradas/atualizadas, %d antigas removidas.', count($keep), $removed));
    }

    private function syncResearchers(SymfonyStyle $io): void
    {
        $io->section('Pesquisadores');
        $repository = $this->entityManager->getRepository(Researcher::class);

        $existing = [];
        foreach ($repository->findAll() as $researcher) {
            $existing[$this->normalizeName((string) $researcher->getNome())] = $researcher;
        }

        $kept = [];
        $created = 0;
        foreach (self::researchers() as $position => [$name, $institution]) {
            $key = $this->normalizeName($name);
            $researcher = $existing[$key] ?? null;
            if (!$researcher) {
                $researcher = new Researcher();
                $created++;
            }
            $researcher->setNome($name)->setInstitution($institution)->setPosition($position + 1);
            $this->entityManager->persist($researcher);
            $kept[$key] = true;
        }

        $removed = [];
        foreach ($existing as $key => $researcher) {
            if (!isset($kept[$key])) {
                $removed[] = $researcher->getNome();
                $this->entityManager->remove($researcher);
            }
        }

        $io->writeln(sprintf('%d pesquisadores (%d novos, %d atualizados).', count($kept), $created, count($kept) - $created));
        if ($removed) {
            $io->writeln('Removidos (não constam na lista do cliente): ' . implode(', ', $removed));
        }
    }

    private function removePlaceholderProjects(SymfonyStyle $io): void
    {
        $io->section('Projetos de exemplo');
        $repository = $this->entityManager->getRepository(Project::class);

        foreach (self::PLACEHOLDER_PROJECT_SLUGS as $slug) {
            $project = $repository->findOneBy(['slugPt' => $slug]);
            if (!$project) {
                continue;
            }
            if ($project->getDocuments()->count() > 0) {
                $io->warning(sprintf('Projeto "%s" mantido: possui %d documento(s) vinculado(s).', $project->getNomePt(), $project->getDocuments()->count()));
                continue;
            }
            $this->entityManager->remove($project);
            $io->writeln('Removido: ' . $project->getNomePt());
        }
    }

    private function syncBanners(SymfonyStyle $io, bool $dryRun): void
    {
        $io->section('Banners da home');
        $repository = $this->entityManager->getRepository(HomeBanner::class);
        $banners = $repository->findBy([], ['position' => 'ASC', 'id' => 'ASC']);

        $first = $banners[0] ?? new HomeBanner();
        $first->setTitlePt('CPA Citros')
            ->setTitleEn('CPA Citros')
            ->setSubtitlePt('Centro de Pesquisa Aplicada em Inovação e Sustentabilidade da Citricultura')
            ->setSubtitleEn('Applied Research Center for Innovation and Sustainability in Citriculture')
            ->setIsActive(true)
            ->setPosition(1);
        if (!$first->getImage()?->getImageName()) {
            $first->setImage($this->copyLayoutImage('banner1.jpg', $dryRun));
        }
        $this->entityManager->persist($first);

        $phrasePt = 'Desenvolvimento científico e inovação tecnológica aplicados à sustentabilidade da citricultura e ao enfrentamento estratégico do HLB';
        $second = $repository->findOneBy(['titlePt' => $phrasePt]) ?? ($banners[1] ?? new HomeBanner());
        $second->setTitlePt($phrasePt)
            ->setTitleEn('Scientific development and technological innovation applied to citrus sustainability and the strategic fight against HLB')
            ->setSubtitlePt(null)
            ->setSubtitleEn(null)
            ->setIsActive(true)
            ->setPosition(2);
        if (!$second->getImage()?->getImageName()) {
            $second->setImage($this->copyLayoutImage('banner2.jpg', $dryRun));
        }
        $this->entityManager->persist($second);

        $io->writeln('Banner 1: CPA Citros + nome completo. Banner 2: frase institucional.');
    }

    private function copyLayoutImage(string $fileName, bool $dryRun): ?Image
    {
        $source = $this->projectDir . '/public/images/' . $fileName;
        if (!is_file($source)) {
            return null;
        }

        $newName = bin2hex(random_bytes(8)) . '-' . $fileName;
        if (!$dryRun) {
            $destinationDir = $this->projectDir . '/public/uploads';
            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0775, true);
            }
            copy($source, $destinationDir . '/' . $newName);
        }

        $image = new Image();
        $image->setImageName($newName);
        $image->setType('banner');
        $this->entityManager->persist($image);

        return $image;
    }

    private function normalizeName(string $name): string
    {
        $name = preg_replace(self::HONORIFICS, '', trim($name));
        $name = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $name);

        return preg_replace('/\s+/', ' ', $name);
    }

    private static function researchStructure(): array
    {
        return [
            [
                'pt' => 'Manejo integrado e sustentável de doenças e pragas dos citros',
                'en' => 'Integrated and sustainable management of citrus diseases and pests',
                'lines' => [
                    [
                        'pt' => 'Manejo de doenças e pragas',
                        'en' => 'Disease and pest management',
                        'descPt' => 'Estratégias de resistência, controle químico, biológico, físico e cultural para proteger os pomares.',
                        'descEn' => 'Resistance, chemical, biological, physical and cultural control strategies to protect orchards.',
                        'modules' => [
                            ['Resistência da planta e melhoramento', 'Plant resistance and breeding'],
                            ['Controle químico', 'Chemical control'],
                            ['Controle biológico, físico e cultural', 'Biological, physical and cultural control'],
                        ],
                    ],
                    [
                        'pt' => 'Entendimento das interações patógeno-planta-vetor',
                        'en' => 'Understanding pathogen–plant–vector interactions',
                        'descPt' => 'Como a bactéria, a planta e o psilídeo interagem — da fisiologia à genética e ao clima.',
                        'descEn' => 'How the bacterium, the plant and the psyllid interact — from physiology to genetics and climate.',
                        'modules' => [
                            ['Fisiologia e metabolismo', 'Physiology and metabolism'],
                            ['Bioquímica da interação', 'Biochemistry of the interaction'],
                            ['Genética da interação', 'Genetics of the interaction'],
                            ['Fisiologia da interação', 'Physiology of the interaction'],
                            ['Histopatologia da interação', 'Histopathology of the interaction'],
                            ['Mudanças climáticas na interação', 'Climate change and the interaction'],
                        ],
                    ],
                    [
                        'pt' => 'Aumento de produção e mitigação de danos',
                        'en' => 'Yield increase and damage mitigation',
                        'descPt' => 'Sistemas de produção, nutrição e avaliação de riscos para manter pomares produtivos.',
                        'descEn' => 'Production systems, nutrition and risk assessment to keep orchards productive.',
                        'modules' => [
                            ['Sistemas de produção', 'Production systems'],
                            ['Nutrição e redução de perdas', 'Nutrition and loss reduction'],
                            ['Avaliação de riscos e perdas', 'Risk and loss assessment'],
                        ],
                    ],
                ],
            ],
            [
                'pt' => 'Educação, difusão do conhecimento e transferência de tecnologia',
                'en' => 'Education, knowledge dissemination and technology transfer',
                'lines' => [
                    [
                        'pt' => 'Educação e difusão do conhecimento',
                        'en' => 'Education and knowledge dissemination',
                        'descPt' => 'Formação de pessoas e comunicação dos resultados da pesquisa para o setor e a sociedade.',
                        'descEn' => 'Training people and communicating research results to the sector and society.',
                        'modules' => [],
                    ],
                    [
                        'pt' => 'Transferência de tecnologia',
                        'en' => 'Technology transfer',
                        'descPt' => 'Levar as soluções desenvolvidas pelo centro ao campo, junto com produtores e técnicos.',
                        'descEn' => 'Bringing the solutions developed by the center to the field, together with growers and technicians.',
                        'modules' => [],
                    ],
                ],
            ],
        ];
    }

    /** @return array<int, array{0: string, 1: string, 2: string, 3: string, 4: string, 5: float, 6: float}> */
    private static function partners(): array
    {
        return [
            ['ESALQ-USP', 'Escola Superior de Agricultura “Luiz de Queiroz”', 'https://www.esalq.usp.br', 'BR', 'Piracicaba (SP)', -22.7089, -47.6317],
            ['FUNDECITRUS', 'Fundo de Defesa da Citricultura', 'https://www.fundecitrus.com.br', 'BR', 'Araraquara (SP)', -21.7946, -48.1756],
            ['APTA-SAA/SP', 'Apta Regional de Piracicaba', 'https://agricultura.sp.gov.br/apta-regional/piracicaba', 'BR', 'Piracicaba (SP)', -22.7000, -47.6500],
            ['CCBS-UFSCar', 'Centro de Ciências Biológicas e da Saúde', 'https://www.ccbs.ufscar.br/pt-br', 'BR', 'São Carlos (SP)', -21.9840, -47.8814],
            ['CCET-UFSCar', 'Centro de Ciências Exatas e de Tecnologia', 'https://www.ccet.ufscar.br/pt-br', 'BR', 'São Carlos (SP)', -21.9835, -47.8800],
            ['CCSM-IAC', 'Centro de Citricultura Sylvio Moreira', 'https://ccsm.br', 'BR', 'Cordeirópolis (SP)', -22.4817, -47.4567],
            ['CENA-USP', 'Centro de Energia Nuclear na Agricultura', 'https://www.cena.usp.br', 'BR', 'Piracicaba (SP)', -22.7069, -47.6436],
            ['CNPTIA-Embrapa', 'Embrapa Agricultura Digital', 'https://www.embrapa.br/agricultura-digital', 'BR', 'Campinas (SP)', -22.8176, -47.0694],
            ['CNPMF-Embrapa', 'Embrapa Mandioca e Fruticultura', 'https://www.embrapa.br/mandioca-e-fruticultura', 'BR', 'Cruz das Almas (BA)', -12.6694, -39.1017],
            ['CENARGEN-Embrapa', 'Embrapa Recursos Genéticos e Biotecnologia', 'https://www.embrapa.br/recursos-geneticos-e-biotecnologia', 'BR', 'Brasília (DF)', -15.7289, -47.8997],
            ['FCAV-Unesp', 'Faculdade de Ciências Agrárias e Veterinárias', 'https://www.fcav.unesp.br', 'BR', 'Jaboticabal (SP)', -21.2437, -48.2891],
            ['FCFRP-USP', 'Faculdade de Ciências Farmacêuticas de Ribeirão Preto', 'https://fcfrp.usp.br', 'BR', 'Ribeirão Preto (SP)', -21.1661, -47.8497],
            ['FZEA-USP', 'Faculdade de Zootecnia e Engenharia de Alimentos', 'https://www.fzea.usp.br', 'BR', 'Pirassununga (SP)', -21.9486, -47.4556],
            ['IB-SAA/SP', 'Instituto Biológico de São Paulo', 'https://biologico.agricultura.sp.gov.br', 'BR', 'São Paulo (SP)', -23.5869, -46.6464],
            ['IB-Unesp', 'Instituto de Biociências', 'https://ib.rc.unesp.br', 'BR', 'Rio Claro (SP)', -22.3956, -47.5431],
            ['IB-Unicamp', 'Instituto de Biologia', 'https://www.ib.unicamp.br', 'BR', 'Campinas (SP)', -22.8190, -47.0697],
            ['IQ-Unicamp', 'Instituto de Química', 'https://www.iqm.unicamp.br', 'BR', 'Campinas (SP)', -22.8196, -47.0685],
            ['CIRAD', 'Centre de Coopération Internationale en Recherche Agronomique pour le Développement', 'https://www.cirad.fr/en', 'FR', 'Montpellier', 43.6453, 3.8653],
            ['CREC-UF/IFAS', 'Citrus Research and Education Center — University of Florida', 'https://crec.ifas.ufl.edu', 'US', 'Lake Alfred, Flórida', 28.1024, -81.7137],
            ['ICA-CSIC', 'Consejo Superior de Investigaciones Científicas — Instituto de Ciencias Agrarias', 'https://www.ica.csic.es', 'ES', 'Madri', 40.4406, -3.6886],
            ['FCT-UAlg', 'Faculdade de Ciências e Tecnologia — Universidade do Algarve', 'https://fct.ualg.pt', 'PT', 'Faro', 37.0442, -7.9730],
            ['IFAPA', 'Instituto Andaluz de Investigación y Formación Agraria, Pesquera, Alimentaria y de la Producción Ecológica', 'https://www.juntadeandalucia.es/agriculturaypesca/ifapa/web', 'ES', 'Sevilha', 37.3891, -5.9845],
            ['IBMCP', 'Instituto de Biología Molecular y Celular de Plantas', 'https://ibmcp.upv.es', 'ES', 'Valência', 39.4817, -0.3413],
            ['DAF', 'Queensland Department of Agriculture and Fisheries', 'https://www.agriculture.gov.au', 'AU', 'Brisbane', -27.4698, 153.0251],
            ['UGR', 'Universidad de Granada', 'https://www.ugr.es', 'ES', 'Granada', 37.1840, -3.6017],
            ['UPV', 'Universitat Politècnica de València', 'https://www.upv.es', 'ES', 'Valência', 39.4815, -0.3400],
            ['UC Davis', 'University of California, Davis', 'https://biology.ucdavis.edu', 'US', 'Davis, Califórnia', 38.5382, -121.7617],
            ['Cambridge', 'University of Cambridge', 'https://www.plantsci.cam.ac.uk', 'GB', 'Cambridge', 52.2043, 0.1149],
            ['Warwick', 'University of Warwick', 'https://warwick.ac.uk/fac/sci/lifesci', 'GB', 'Coventry', 52.3793, -1.5615],
        ];
    }

    /** @return array<int, array{0: string, 1: string}> */
    private static function researchers(): array
    {
        return [
            ['Alberto Fereres Castiel', 'CSIC/Espanha'],
            ['Alécio Souza Moreira', 'CNPTIA/Embrapa'],
            ['Alessandro de Mello Varani', 'FCAV/Unesp Jaboticabal'],
            ['Aline Sartori Guidolin', 'Esalq/USP'],
            ['Amílcar Manuel Marreiros Duarte', 'Univ. do Algarve/Portugal'],
            ['Amit Levy', 'CREC/Univ. Florida/EUA'],
            ['Andréia Cristina de Oliveira Adami', 'CEPEA/USP'],
            ['Andreia Soares Costa Fuentes', 'CCBS/UFSCar'],
            ['Antonio Vargas de Oliveira Figueira', 'CENA/USP'],
            ['Armando Bergamin Filho', 'Esalq/USP'],
            ['Arthur Fernando Tomaseto', 'Fundecitrus'],
            ['Barbara Hufnagel', 'CIRAD/França'],
            ['Beatriz Appezzato-da-Glória', 'Esalq/USP'],
            ['Carlos Henrique Tomich de Paula da Silva', 'FCFRP/USP'],
            ['Celso Omoto', 'Esalq/USP'],
            ['Choaa El-Mohtar', 'CREC/Univ. Florida/EUA'],
            ['Daniel de Castro Victoria', 'CNPTIA/Embrapa'],
            ['Daniel Scherer de Moura', 'Esalq/USP'],
            ['Daniela Kharfan', 'JBT'],
            ['Dirceu de Mattos Junior', 'CCSM/IAC'],
            ['Douglas Silva Domingues', 'Esalq/USP'],
            ['Eduardo Augusto Girardi', 'Fundecitrus/UMIPTT'],
            ['Elaine Fitches', 'Univ. Durham/Inglaterra'],
            ['Eliane Cristina Locali', 'Fundecitrus'],
            ['Elliot Watanabe Kitajima', 'Esalq/USP'],
            ['Fábio Ricardo Marin', 'Esalq/USP'],
            ['Fábio Tebaldi Silveira Nogueira', 'Esalq/USP'],
            ['Fernando Alves de Azevedo', 'CCSM/IAC'],
            ['Fernando Javier Sanhueza Salas', 'Instituto Biológico'],
            ['Fernando Luis Cônsoli', 'Esalq/USP'],
            ['Francisco Ferraz Laranjeira Barbosa', 'CNPMF/Embrapa'],
            ['Francisco José Arenas Arenas', 'IFAPA/Espanha'],
            ['Francisco José Lima Aragão', 'CENARGEN/Embrapa'],
            ['Franklin Behlau', 'Fundecitrus'],
            ['Geraldo José Silva Jr.', 'Fundecitrus'],
            ['Haroldo Xavier Linhares Volpe', 'Fundecitrus'],
            ['Henrique Ferreira', 'IBC/Unesp Rio Claro'],
            ['Ítalo Delalibera Junior', 'Esalq/USP'],
            ['Ivaldo Sala', 'Fundecitrus'],
            ['Jaqueline Franciosi Della Vechia', 'Fundecitrus'],
            ['Jesus Aparecido Ferro', 'FCAV/Unesp Jaboticabal'],
            ['João Paulo Rodrigues Marques', 'FZEA/USP'],
            ['João Roberto Spotti Lopes', 'Esalq/USP'],
            ['José Antonio Alberto da Silva', 'APTA'],
            ['José Maurício Simões Bento', 'Esalq/USP'],
            ['Juan Camilo Cifuentes-Arenas', 'Fundecitrus'],
            ['Juliano Quarteroli Silva', 'Coordenadoria de Assistência Técnica Integral (CATI)'],
            ['Leandro Antonio Peña Garcia', 'CSIC/Espanha'],
            ['Lilian Amorim', 'Esalq/USP'],
            ['Luis Eduardo Aranha Camargo', 'Esalq/USP'],
            ['Luis Fernado Bianco', 'Coordenadoria de Defesa Agropecuária (CDA)'],
            ['Malcolm Smith', 'DAF/Austrália'],
            ['Marcelo Pedreira de Miranda', 'Fundecitrus'],
            ['Maria Teresa Marques Novo Mansur', 'CCBS/UFSCar'],
            ['Mariângela Cristofani-Yaly', 'CCSM/IAC'],
            ['Marinês Bastianel', 'CCSM/IAC'],
            ['Michele do Carmo de Souza Timossi', 'Fundecitrus'],
            ['Moacir Rossi Forim', 'CCET/UFSCar'],
            ['Mônica Neli Alves', 'Fundecitrus'],
            ['Nelson Arno Wulff', 'Fundecitrus'],
            ['Nik Cunniffe', 'Univ. Cambridge/Inglaterra'],
            ['Patrick Ollitrault', 'CIRAD/França'],
            ['Paulo José Pereira Lima Teixeira', 'Esalq/USP'],
            ['Pedro Takao Yamamoto', 'Esalq/USP'],
            ['Rafael Vasconcelos Ribeiro', 'IB/Unicamp'],
            ['Raphael Morillon', 'CIRAD/França'],
            ['Renato Beozzo Bassanezi', 'Fundecitrus'],
            ['Rodrigo Facchini Magnani', 'Fundecitrus'],
            ['Rodrigo Marcelli Boaretto', 'CCSM/IAC'],
            ['Sílvia Helena Galvão de Miranda', 'Esalq/USP'],
            ['Sílvio Aparecido Lopes', 'Fundecitrus'],
            ['Sônia Ternes', 'CNPTIA/Embrapa'],
            ['Stephen Parnell', 'Univ. Warwick/Inglaterra'],
            ['Taicia Pacheco Fill', 'IQ/Unicamp'],
            ['Tripti Vashisth', 'CREC/Univ. Florida/EUA'],
            ['Walter Soares Leal', 'UC Davis/EUA'],
            ['Wellington Ivo Eduardo', 'Fundecitrus'],
        ];
    }
}
