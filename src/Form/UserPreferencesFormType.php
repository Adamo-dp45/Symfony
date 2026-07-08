<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserPreferencesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme', ChoiceType::class, [
                'label' => 'Thème',
                'choices' => [
                    'Clair' => 'light',
                    'Sombre' => 'dark',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('notifications', CheckboxType::class, [
                'label' => 'Recevoir des notifications',
                'required' => false,
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'Langue',
                'choices' => [
                    'Français' => 'fr',
                    'Anglais' => 'en',
                    'Espagnol' => 'es',
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
