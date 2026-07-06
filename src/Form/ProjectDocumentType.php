<?php

namespace App\Form;

use App\Entity\ProjectDocument;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProjectDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'nomePt',
                'label' => 'Projeto Científico Relacionado',
            ])
            ->add('title', null, [
                'label' => 'Título do Documento',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Tipo de Documento',
                'choices' => [
                    'Artigo' => 'artigo',
                    'Abstract' => 'abstract',
                    'Dissertação' => 'dissertação',
                ],
            ])
            ->add('year', null, [
                'label' => 'Ano de Publicação',
            ])
            ->add('researcher', null, [
                'label' => 'Pesquisador Responsável (campo de texto)',
            ])
            ->add('doi', null, [
                'label' => 'Link DOI (opcional)',
            ])
            ->add('fileFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Marque para excluir o arquivo atual',
                'download_uri' => false,
                'asset_helper' => true,
                'label' => 'Arquivo PDF (opcional se houver DOI)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectDocument::class,
        ]);
    }
}
