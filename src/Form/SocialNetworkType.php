<?php

namespace App\Form;

use App\Entity\SocialNetwork;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialNetworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nome da Rede Social',
                'attr' => ['placeholder' => 'Ex: Instagram, LinkedIn'],
            ])
            ->add('url', TextType::class, [
                'label' => 'URL do Perfil',
                'attr' => ['placeholder' => 'Ex: https://instagram.com/seu_perfil'],
            ])
            ->add('iconClass', TextType::class, [
                'label' => 'Classe do Ícone FontAwesome',
                'attr' => ['placeholder' => 'Ex: fa-brands fa-instagram'],
                'required' => false,
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Ativo / Exibir no rodapé',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialNetwork::class,
        ]);
    }
}
