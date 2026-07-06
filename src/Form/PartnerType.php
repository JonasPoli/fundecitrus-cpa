<?php

namespace App\Form;

use App\Entity\Partner;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nome'])
            ->add('logo', ImageType::class, [
                'label' => 'Logotipo (opcional se usar ícone)',
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
            ->add('region', ChoiceType::class, [
                'label' => 'Região',
                'choices' => [
                    'Nacional (Brasil)' => 'BR',
                    'Internacional (Global)' => 'INT',
                ],
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
