<?php

namespace App\Form;

use App\Entity\Researcher;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
                'label' => 'Link do Currículo Lattes',
                'required' => false,
            ])
            ->add('linkedin', null, [
                'label' => 'Link do LinkedIn',
                'required' => false,
            ])
            ->add('email', null, [
                'label' => 'E-mail',
                'required' => false,
            ])
            ->add('foto', ImageType::class, [
                'label' => 'Foto',
                'required' => false,
            ])
            ->add('curriculoPt', TextareaType::class, [
                'label' => 'Currículo (Português)',
                'attr' => ['class' => 'quill-textarea'],
                'required' => false,
            ])
            ->add('curriculoEn', TextareaType::class, [
                'label' => 'Currículo (Inglês)',
                'attr' => ['class' => 'quill-textarea'],
                'required' => false,
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
