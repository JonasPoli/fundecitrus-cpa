<?php

namespace App\Form;

use App\Entity\ResearchLine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResearchLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('namePt', null, ['label' => 'Linha de Pesquisa (Português)'])
            ->add('nameEn', null, ['label' => 'Linha de Pesquisa (Inglês)'])
            ->add('descriptionPt', TextareaType::class, [
                'label' => 'Descrição curta (Português) — opcional',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('descriptionEn', TextareaType::class, [
                'label' => 'Descrição curta (Inglês) — opcional',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('image', ImageType::class, [
                'label' => 'Imagem de fundo da caixa (foto na vertical ou quadrada, mín. 800 px)',
                'required' => false,
            ])
            ->add('modules', CollectionType::class, [
                'entry_type' => ResearchModuleType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ResearchLine::class]);
    }
}
