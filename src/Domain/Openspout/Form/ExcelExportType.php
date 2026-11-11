<?php

namespace App\Domain\Openspout\Form;

use App\Domain\Openspout\Enum\ExcelFormatEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExcelExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('format', EnumType::class, [
                'class' => ExcelFormatEnum::class,
                'placeholder' => 'Sélectionner un format'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Soumettre'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
