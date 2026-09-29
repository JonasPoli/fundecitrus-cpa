<?php

namespace App\Form;

use App\Entity\EventRegistration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class EventRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $en = $options['locale'] === 'en';

        $builder
            ->add('name', null, [
                'label' => $en ? 'Full name' : 'Nome completo',
                'attr' => ['autocomplete' => 'name'],
            ])
            ->add('institution', null, [
                'label' => $en ? 'Institution' : 'Instituição',
                'attr' => ['autocomplete' => 'organization'],
            ])
            ->add('role', null, [
                'label' => $en ? 'Role / Position' : 'Função',
                'attr' => ['autocomplete' => 'organization-title'],
            ])
            ->add('city', null, [
                'label' => $en ? 'City' : 'Cidade',
                'attr' => ['autocomplete' => 'address-level2'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => ['autocomplete' => 'email', 'inputmode' => 'email'],
            ])
            ->add('phone', TelType::class, [
                'label' => $en ? 'Phone' : 'Telefone',
                'attr' => ['autocomplete' => 'tel', 'inputmode' => 'tel'],
            ])
            ->add('file', VichFileType::class, [
                'label' => $en ? 'Attachment (PDF, DOC, JPG or PNG, up to 10 MB)' : 'Arquivo (PDF, DOC, JPG ou PNG, até 10 MB)',
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventRegistration::class,
            'locale' => 'pt',
        ]);
    }
}
