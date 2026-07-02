<?php

namespace App\Form;

use App\Entity\HomeBanner;
use App\Entity\Image;
use App\Form\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomeBannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titlePt', null, [
                'label' => 'Título (Português)'])
            ->add('titleEn', null, [
                'label' => 'Título (Inglês)'])
            ->add('subtitlePt', null, [
                'label' => 'Subtítulo (Português)'])
            ->add('subtitleEn', null, [
                'label' => 'Subtítulo (Inglês)'])
            ->add('buttonTextPt', null, [
                'label' => 'Texto do Botão (Português)'])
            ->add('buttonTextEn', null, [
                'label' => 'Texto do Botão (Inglês)'])
            ->add('buttonLink', null, [
                'label' => 'Link do Botão'])
            ->add('isActive', null, [
                'label' => 'Ativo?'])
            ->add('image', ImageType::class, [
                'label' => 'Imagem de Fundo',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HomeBanner::class,
        ]);
    }
}
