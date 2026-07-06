<?php

namespace App\Form;

use App\Entity\YoutubeMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YoutubeMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Tipo de Mídia',
                'choices' => [
                    'Vídeo (YouTube)' => 'video',
                    'Podcast (Entrevista/Som)' => 'podcast',
                ],
            ])
            ->add('youtubeId', null, [
                'label' => 'ID do Vídeo do YouTube (ex: dQw4w9WgXcQ)',
                'attr' => ['placeholder' => 'Insira apenas o código de 11 caracteres após v='],
            ])
            ->add('title', null, [
                'label' => 'Título',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descrição',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => YoutubeMedia::class,
        ]);
    }
}
