<?php

namespace App\Form;

use App\Entity\GovernanceMember;
use App\Enum\GovernanceGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GovernanceMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('governanceGroup', EnumType::class, [
                'class' => GovernanceGroup::class,
                'choice_label' => fn (GovernanceGroup $group) => $group->label(),
                'label' => 'Grupo do fluxograma',
                'placeholder' => 'Selecione o grupo...',
            ])
            ->add('titlePt', null, ['label' => 'Título (Português) — ex.: Dra. Lilian Amorim, Fapesp'])
            ->add('titleEn', null, [
                'label' => 'Título (Inglês) — opcional, se vazio usa o português',
                'required' => false,
            ])
            ->add('descriptionPt', TextareaType::class, [
                'label' => 'Descrição (Português) — opcional, ex.: Diretora (Esalq-USP)',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('descriptionEn', TextareaType::class, [
                'label' => 'Descrição (Inglês) — opcional, se vazio usa o português',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('image', ImageType::class, [
                'label' => 'Imagem — opcional (foto da pessoa ou logo da instituição)',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => GovernanceMember::class]);
    }
}
