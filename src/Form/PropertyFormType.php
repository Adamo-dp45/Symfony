<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Property;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PropertyFormType extends AbstractType
{
    public function __construct(private UrlGeneratorInterface $url)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('surface')
            ->add('rooms')
            ->add('bedrooms')
            ->add('floor')
            ->add('price')
            ->add('heat', ChoiceType::class, [
                'choices' => $this->getChoices()
            ])
            // ->add('options', EntityType::class, [
            //    'class' => Option::class,
            //    'choice_label' => 'name',
            //    'multiple' => true
            // ])
            ->add('options', SearchableEntityType::class, [
                'class' => Option::class,
                'search' => $this->url->generate('api.option'),
                'value_field' => 'id',
                'label_field' => 'name'
            ])
            ->add('city')
            ->add('address')
            ->add('postal_code')
            ->add('sold')
            // ->add('imageFile', FileType::class, [
            //    'required' => false
            // ])
            ->add('pictureFiles', FileType::class, [
                'required' => false,
                'multiple' => true,
                /*
                    new All([
                        new Image([
                            'maxSize' => '5M'
                            'mimeTypes' => ['image/jpeg', 'image/png'],
                            'mimeTypesMessage' => 'Seules les images sont autorisées.',
                            'minWidth' => 200,
                            'maxHeight' => 1080,
                            'maxSizeMessage' => 'L’image ne doit pas dépasser 2 Mo.',
                            'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                            'mimeTypesMessage' => 'Seules les images JPEG, PNG et WEBP sont autorisées.',
                            'minWidth' => 200,
                            'maxWidth' => 2000,
                            'minHeight' => 200,
                            'maxHeight' => 2000,
                            'minWidthMessage' => 'L’image doit avoir au moins {{ min_width }}px de largeur.',
                            'maxWidthMessage' => 'L’image ne doit pas dépasser {{ max_width }}px de largeur.',
                            'minHeightMessage' => 'L’image doit avoir au moins {{ min_height }}px de hauteur.',
                            'maxHeightMessage' => 'L’image ne doit pas dépasser {{ max_height }}px de hauteur.',
                        ])
                    ]
                */
            ])

            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            // 'translation_domain' => 'forms' // Si je veux mettre les labels en français
        ]);
    }

    private function getChoices()
    {
        $choices = Property::HEAD;
        $output = [];
        foreach($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }

}
