<?php

namespace App\Form;

use App\Entity\AboutSection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AboutSectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titlePt', null, ['label' => 'Título (Português)'])
            ->add('titleEn', null, ['label' => 'Título (Inglês)'])
            ->add('contentPt', TextareaType::class, [
                'label' => 'Texto (Português)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('contentEn', TextareaType::class, [
                'label' => 'Texto (Inglês)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('image', ImageType::class, [
                'label' => 'Imagem',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => AboutSection::class]);
    }
}
