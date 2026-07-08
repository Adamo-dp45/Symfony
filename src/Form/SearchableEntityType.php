<?php

namespace App\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchableEntityType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('class'); // On attend une propriété réquise
        $resolver->setDefaults([ // La valeur par défaut
            'compound' => false, // Vu qu'on vas travailler avec quelque chose de type de base, select - input - textarea
            'multiple' => true,

            'search' => '/search', // On vas l'utiliser pour le js
            'value_field' => 'id', // Pour être plus générique
            'label_field' => 'name'
        ]);
    }

    /**
     * Pour transformer les données au niveau du formulaire
     */
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function(Collection $value): array {
                return $value->map(fn($d): string => (string)$d->getId())->toArray();
            },
            function(array $ids) use($options): Collection {
                if(empty($ids)) {
                    return new ArrayCollection([]);
                }
                return new ArrayCollection(
                    $this->em->getRepository($options['class'])->findBy(['id' => $ids])
                );
            }
        ));
    }

    /**
     * $options correspond au options qui vont être passer lorsqu'on initialise notre formType
     */
    public function buildView(\Symfony\Component\Form\FormView $view, \Symfony\Component\Form\FormInterface $form, array $options)
    {
        $view->vars['expanded'] = false;
        $view->vars['placeholder'] = null;
        $view->vars['placeholder_in_choices'] = false; // Permet de glisser les placeholder parmi les choix
        $view->vars['multiple'] = true;
        $view->vars['preferred_choices'] = []; // Permet d'avoir des choix qui vont se placé au début
        $view->vars['choices'] = $this->choices($form->getData()); // L'ensemble des choix que l'utilisateur va pouvoir faire
        $view->vars['choice_translation_domain'] = false; // Est ce qu'on doit utiliser la traduction pour les labels
        $view->vars['full_name'] .= '[]'; // Rajoute à la fin du nom du champ des petit croché vu qu'on veut un select multiple

        $view->vars['attr']['data-remote'] = $options['search']; // Js
        $view->vars['attr']['data-value'] = $options['value_field'];
        $view->vars['attr']['data-label'] = $options['label_field'];
    }

    public function getBlockPrefix()
    {
        return 'choice'; // Permet d'avoir un select lors du rendu du template
    }
 
    private function choices(Collection $value)
    {
        return $value
            ->map(fn($d) => new ChoiceView($d, (string)$d->getId(), (string)$d)) // Ici pour le label on a converti l'entité sous forme de chaine de caractère, si on le fais notre entité doit avoir la méthode __toString()
            ->toArray();
    }
} 