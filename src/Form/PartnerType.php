<?php

namespace App\Form;

use App\Entity\Partner;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nome completo'])
            ->add('acronym', null, [
                'label' => 'Sigla (ex: ESALQ-USP)',
                'required' => false,
            ])
            ->add('country', CountryType::class, [
                'label' => 'País',
                'preferred_choices' => ['BR', 'US', 'ES', 'PT', 'FR', 'GB', 'AU'],
                'choice_translation_locale' => 'pt_BR',
            ])
            ->add('city', null, [
                'label' => 'Cidade',
                'required' => false,
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude (copie do Google Maps, ex: -22.7085)',
                'required' => false,
                'scale' => 7,
                'input' => 'string',
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude (ex: -47.6289)',
                'required' => false,
                'scale' => 7,
                'input' => 'string',
            ])
            ->add('logo', ImageType::class, [
                'label' => 'Logotipo (opcional)',
                'required' => false,
            ])
            ->add('url', null, [
                'label' => 'URL do Site (Link)',
                'required' => false,
            ])
            ->add('newTab', CheckboxType::class, [
                'label' => 'Abrir em nova guia (target="_blank")',
                'required' => false,
            ])
            ->add('iconClass', null, [
                'label' => 'Classe do Ícone (ex: fa-solid fa-building-columns)',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partner::class,
        ]);
    }
}
