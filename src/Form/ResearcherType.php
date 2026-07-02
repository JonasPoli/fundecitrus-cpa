<?php

namespace App\Form;

use App\Entity\Researcher;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResearcherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nome', null, [
                'label' => 'Nome'])
            ->add('areaPt', null, [
                'label' => 'Área de Atuação (Português)'])
            ->add('areaEn', null, [
                'label' => 'Área de Atuação (Inglês)'])
            ->add('lattes', null, [
                'label' => 'Link do Currículo Lattes'])
            ->add('foto', ImageType::class, [
                'label' => 'Foto',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Researcher::class,
        ]);
    }
}
