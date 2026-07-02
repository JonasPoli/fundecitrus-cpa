<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Researcher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomePt', null, [
                'label' => 'Nome (Português)'])
            ->add('nomeEn', null, [
                'label' => 'Nome (Inglês)'])
            ->add('objetivoPt', null, [
                'label' => 'Objetivo (Português)'])
            ->add('objetivoEn', null, [
                'label' => 'Objetivo (Inglês)'])
            ->add('descricaoPt', TextareaType::class, [
                'label' => 'Descrição (Português)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('descricaoEn', TextareaType::class, [
                'label' => 'Descrição (Inglês)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('moduloPt', null, [
                'label' => 'Módulo / Pilar (Português)'])
            ->add('moduloEn', null, [
                'label' => 'Módulo / Pilar (Inglês)'])
            ->add('slugPt', null, [
                'label' => 'Slug (Português)'])
            ->add('slugEn', null, [
                'label' => 'Slug (Inglês)'])
            ->add('pesquisador', EntityType::class, [
                'class' => Researcher::class,
                'choice_label' => 'nome',
                'label' => 'Pesquisador Responsável',
                'placeholder' => 'Selecione o pesquisador...',
                'required' => false,
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
