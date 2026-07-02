<?php

namespace App\Form;

use App\Entity\JobOpportunity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class JobOpportunityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titlePt', null, [
                'label' => 'Título (Português)'])
            ->add('titleEn', null, [
                'label' => 'Título (Inglês)'])
            ->add('statusPt', null, [
                'label' => 'Status (Português)',
                'help' => 'Ex: "Aberto", "Encerrado"'])
            ->add('statusEn', null, [
                'label' => 'Status (Inglês)',
                'help' => 'Ex: "Open", "Closed"'])
            ->add('summaryPt', null, [
                'label' => 'Resumo (Português)'])
            ->add('summaryEn', null, [
                'label' => 'Resumo (Inglês)'])
            ->add('contentPt', TextareaType::class, [
                'label' => 'Descrição da Vaga (Português)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('contentEn', TextareaType::class, [
                'label' => 'Descrição da Vaga (Inglês)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('slugPt', null, [
                'label' => 'Slug (Português)'])
            ->add('slugEn', null, [
                'label' => 'Slug (Inglês)'])
            ->add('image', ImageType::class, [
                'label' => 'Imagem de Destaque',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobOpportunity::class,
        ]);
    }
}
