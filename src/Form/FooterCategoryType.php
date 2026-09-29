<?php

namespace App\Form;

use App\Entity\FooterCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FooterCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('namePt', null, ['label' => 'Nome (Português) — ex: Financiadores'])
            ->add('nameEn', null, ['label' => 'Nome (Inglês) — ex: Funding'])
            ->add('logoSize', ChoiceType::class, [
                'label' => 'Tamanho dos logos desta categoria',
                'choices' => FooterCategory::LOGO_SIZES,
                'help' => 'Use “Pequeno” para dar menos destaque (ex.: Apoio).',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => FooterCategory::class]);
    }
}
