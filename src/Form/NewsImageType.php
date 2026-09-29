<?php

namespace App\Form;

use App\Entity\NewsImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', ImageType::class, ['label' => 'Foto'])
            ->add('captionPt', null, [
                'label' => 'Legenda (Português)',
                'required' => false,
            ])
            ->add('captionEn', null, [
                'label' => 'Legenda (Inglês)',
                'required' => false,
            ])
            ->add('position', HiddenType::class, ['attr' => ['data-collection-position' => '']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => NewsImage::class]);
    }
}
