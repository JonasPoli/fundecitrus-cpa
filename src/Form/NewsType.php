<?php

namespace App\Form;

use App\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titlePt', null, [
                'label' => 'Título (Português)'])
            ->add('titleEn', null, [
                'label' => 'Título (Inglês)'])
            ->add('summaryPt', null, [
                'label' => 'Resumo (Português)'])
            ->add('summaryEn', null, [
                'label' => 'Resumo (Inglês)'])
            ->add('contentPt', TextareaType::class, [
                'label' => 'Conteúdo (Português)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('contentEn', TextareaType::class, [
                'label' => 'Conteúdo (Inglês)',
                'attr' => ['class' => 'quill-textarea'],
            ])
            ->add('slugPt', null, [
                'label' => 'Slug (Português)'])
            ->add('slugEn', null, [
                'label' => 'Slug (Inglês)'])
            ->add('date', null, [
                'label' => 'Data da Notícia',
                'widget' => 'single_text',
            ])
            ->add('image', ImageType::class, [
                'label' => 'Imagem de Destaque',
            ])
            ->add('gallery', CollectionType::class, [
                'entry_type' => NewsImageType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('youtubeVideoCode', null, [
                'label' => 'Código do Vídeo do YouTube (ex: dQw4w9WgXcQ)',
                'required' => false,
            ])
            ->add('seoTitle', null, [
                'label' => 'Título de SEO (Meta Title)',
                'required' => false,
            ])
            ->add('seoDescription', null, [
                'label' => 'Descrição de SEO (Meta Description)',
                'required' => false,
            ])
            ->add('imageAlt', null, [
                'label' => 'Texto Alternativo da Imagem (Alt)',
                'required' => false,
            ])
            ->add('canonicalUrl', null, [
                'label' => 'URL Canônica',
                'required' => false,
            ])
            ->add('isNoIndex', CheckboxType::class, [
                'label' => 'Não Indexar (noindex)',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Publicado' => 'publicado',
                    'Rascunho' => 'rascunho',
                ],
            ])
            ->add('highlighted', CheckboxType::class, [
                'label' => 'Notícia em Destaque',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
