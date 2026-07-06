<?php

namespace App\Form;

use App\Entity\Clipping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ClippingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('veiculo', null, [
                'label' => 'Veículo de Mídia'])
            ->add('titlePt', null, [
                'label' => 'Título (Português)'])
            ->add('titleEn', null, [
                'label' => 'Título (Inglês)'])
            ->add('summaryPt', TextareaType::class, [
                'label' => 'Resumo (Português)',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('summaryEn', TextareaType::class, [
                'label' => 'Resumo (Inglês)',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('link', null, [
                'label' => 'Link Original (URL)'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clipping::class,
        ]);
    }
}
