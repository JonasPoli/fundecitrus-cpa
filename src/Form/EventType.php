<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titlePt', null, [
                'label' => 'Título (Português)'])
            ->add('titleEn', null, [
                'label' => 'Título (Inglês)'])
            ->add('contentPt', TextareaType::class, [
                'label' => 'Conteúdo / Descrição (Português)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('contentEn', TextareaType::class, [
                'label' => 'Conteúdo / Descrição (Inglês)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('datePt', null, [
                'label' => 'Data / Período (Português)',
                'help' => 'Ex: "10 a 12 de Outubro de 2026"'])
            ->add('dateEn', null, [
                'label' => 'Data / Período (Inglês)',
                'help' => 'Ex: "October 10-12, 2026"'])
            ->add('slugPt', null, [
                'label' => 'Slug (Português)'])
            ->add('slugEn', null, [
                'label' => 'Slug (Inglês)'])
            ->add('registrationLink', null, [
                'label' => 'Link para Inscrição (URL)',
                'required' => false,
            ])
            ->add('image', ImageType::class, [
                'label' => 'Imagem de Destaque',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
