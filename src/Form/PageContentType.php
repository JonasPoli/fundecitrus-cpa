<?php

namespace App\Form;

use App\Entity\PageContent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PageContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titlePt', null, [
                'label' => 'Título (Português)'])
            ->add('titleEn', null, [
                'label' => 'Título (Inglês)'])
            ->add('slugPt', null, [
                'label' => 'Slug (Português)'])
            ->add('slugEn', null, [
                'label' => 'Slug (Inglês)'])
            ->add('contentPt', TextareaType::class, [
                'label' => 'Conteúdo (Português)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('contentEn', TextareaType::class, [
                'label' => 'Conteúdo (Inglês)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('isActive', null, [
                'label' => 'Ativo?'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageContent::class,
        ]);
    }
}
