<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vich\UploaderBundle\Form\Type\VichFileType;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titlePt', null, [
                'label' => 'Título (Português)'])
            ->add('titleEn', null, [
                'label' => 'Título (Inglês)'])
            ->add('folderPt', null, [
                'label' => 'Pasta / Categoria (Português)',
                'help' => 'Ex: "Relatórios de HLB", "Dados Primários"'])
            ->add('folderEn', null, [
                'label' => 'Pasta / Categoria (Inglês)',
                'help' => 'Ex: "HLB Reports", "Primary Data"'])
            ->add('fileFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Marque para excluir o arquivo atual',
                'download_uri' => false,
                'asset_helper' => true,
                'label' => 'Arquivo Científico (PDF, ZIP, DOCX)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
