<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Researcher;
use App\Entity\ResearchLine;
use App\Entity\ResearchModule;
use App\Enum\ProjectStatus;
use App\Repository\ResearcherRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomePt', TextareaType::class, [
                'label' => 'Nome (Português)',
                'attr' => ['class' => 'quill-inline'],
            ])
            ->add('nomeEn', TextareaType::class, [
                'label' => 'Nome (Inglês)',
                'attr' => ['class' => 'quill-inline'],
            ])
            ->add('objetivoPt', null, [
                'label' => 'Objetivo (Português) — opcional',
                'required' => false,
            ])
            ->add('objetivoEn', null, [
                'label' => 'Objetivo (Inglês) — opcional',
                'required' => false,
            ])
            ->add('descricaoPt', TextareaType::class, [
                'label' => 'Descrição (Português) — opcional',
                'attr' => ['class' => 'quill-textarea'],
                'required' => false,
            ])
            ->add('descricaoEn', TextareaType::class, [
                'label' => 'Descrição (Inglês) — opcional',
                'attr' => ['class' => 'quill-textarea'],
                'required' => false,
            ])
            ->add('justificativaPt', TextareaType::class, [
                'label' => 'Justificativa (Português) — opcional',
                'attr' => ['class' => 'quill-textarea'],
                'required' => false,
            ])
            ->add('justificativaEn', TextareaType::class, [
                'label' => 'Justificativa (Inglês) — opcional',
                'attr' => ['class' => 'quill-textarea'],
                'required' => false,
            ])
            ->add('resultadosParciaisPt', TextareaType::class, [
                'label' => 'Resultados Parciais (Português) — opcional',
                'attr' => ['class' => 'quill-textarea'],
                'required' => false,
            ])
            ->add('resultadosParciaisEn', TextareaType::class, [
                'label' => 'Resultados Parciais (Inglês) — opcional',
                'attr' => ['class' => 'quill-textarea'],
                'required' => false,
            ])
            ->add('researchLine', EntityType::class, [
                'class' => ResearchLine::class,
                'choice_label' => 'namePt',
                'group_by' => fn (ResearchLine $line) => $line->getArea()?->getNamePt(),
                'label' => 'Eixo de Pesquisa',
                'placeholder' => 'Selecione o eixo...',
                'required' => false,
            ])
            ->add('researchModule', EntityType::class, [
                'class' => ResearchModule::class,
                'choice_label' => 'namePt',
                'group_by' => fn (ResearchModule $module) => $module->getLine()?->getNamePt(),
                'label' => 'Módulo (opcional — define o eixo automaticamente)',
                'placeholder' => 'Sem módulo específico',
                'required' => false,
            ])
            ->add('status', EnumType::class, [
                'class' => ProjectStatus::class,
                'choice_label' => fn (ProjectStatus $status) => $status->label(),
                'label' => 'Status',
                'required' => true,
            ])
            ->add('slugPt', null, [
                'label' => 'Slug (Português)'])
            ->add('slugEn', null, [
                'label' => 'Slug (Inglês)'])
            ->add('pesquisador', EntityType::class, [
                'class' => Researcher::class,
                'choice_label' => 'nome',
                'query_builder' => fn (ResearcherRepository $researcherRepository) => $researcherRepository->createQueryBuilder('r')->orderBy('r.nome', 'ASC'),
                'label' => 'Pesquisador Responsável',
                'placeholder' => 'Selecione o pesquisador...',
                'required' => false,
            ])
            ->add('pesquisadores', EntityType::class, [
                'class' => Researcher::class,
                'choice_label' => 'nome',
                'query_builder' => fn (ResearcherRepository $researcherRepository) => $researcherRepository->createQueryBuilder('r')->orderBy('r.nome', 'ASC'),
                'multiple' => true,
                'label' => 'Outros Pesquisadores — opcional',
                'required' => false,
                'attr' => ['data-researchers-select' => 'true'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
