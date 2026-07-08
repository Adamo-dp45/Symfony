<?php

namespace App\Form;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class CategoryAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Category::class,
            'placeholder' => 'Choisir vos categorie',
            'choice_label' => 'name', /*
                - Le champ utiliser pour le nom
            */
            // 'security' => 'ROLE_SOMETHING', -- Pour permettre à certains de pouvoir lister ces éléments

            // choose which fields to use in the search -- !! indiquer les champs qu'on peut rechercher
            // if not passed, *all* fields are used
            // 'searchable_fields' => ['name'],

            'query_builder' => function (CategoryRepository $categoryRepository) {
                return $categoryRepository->createQueryBuilder('c');
            }, /*
                - On peut personnaliser le 'query_builder' sachant que par défaut il y'a une limit à 10 résultats
            */
            'loading_more_text' => 'Charger plus..',
            'max_results' => 20, // Par défaut '10'
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
