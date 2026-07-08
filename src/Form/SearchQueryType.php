<?php

namespace App\Form;

use App\DTO\SearchQueryDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchQueryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'required' => false,
                'label' => 'Recherche'
            ])
            ->add('page', IntegerType::class, ['required' => false])
            ->add('limit', IntegerType::class, ['required' => false])
            ->add('sortBy', TextType::class, ['required' => false])
            ->add('order', ChoiceType::class, [
                'choices' => [
                    'Ascendant' => 'asc',
                    'Descendant' => 'desc',
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchQueryDto::class,
            'method' => 'GET', // Utile pour un formulaire de recherche
            'csrf_protection' => false,
        ]);
    }

    /**
     * Pour avoir une url propre
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
