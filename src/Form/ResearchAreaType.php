<?php

namespace App\Form;

use App\Entity\ResearchArea;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResearchAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('namePt', null, ['label' => 'Nome (Português)'])
            ->add('nameEn', null, ['label' => 'Nome (Inglês)'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ResearchArea::class]);
    }
}
