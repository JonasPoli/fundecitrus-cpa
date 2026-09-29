<?php

namespace App\Form;

use App\Entity\FooterCategory;
use App\Entity\FooterCompany;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FooterCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => FooterCategory::class,
                'choice_label' => 'namePt',
                'label' => 'Categoria',
                'placeholder' => 'Selecione a categoria...',
            ])
            ->add('name', null, ['label' => 'Nome da empresa / instituição'])
            ->add('url', UrlType::class, [
                'label' => 'Site (URL)',
                'required' => false,
                'default_protocol' => 'https',
            ])
            ->add('newTab', CheckboxType::class, [
                'label' => 'Abrir o site em outra aba',
                'required' => false,
            ])
            ->add('logo', ImageType::class, [
                'label' => 'Logotipo (PNG ou SVG com fundo transparente; sem logo, aparece o nome)',
                'required' => false,
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Exibir no rodapé',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => FooterCompany::class]);
    }
}
