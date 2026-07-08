<?php

namespace App\Form;

use App\Domain\Transformer\RolesToStringTransformer;
use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\Constraint\IsFalse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RecipeFormType extends AbstractType
{
    public function __construct(private FormListenerFactory $factory) 
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'contactForm.title',
                'empty_data' => '', /*
                    - Va mettre '' si le champ est null
                */
                /*
                    'data' => new Date(), -- Pour entrer des données par défaut
                    'label_html' => true, -- !! injecter du HTML directement dans le label
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Entrez votre nom'
                    ],
                    'label_attr' => [
                        'class' => 'form-label'
                    ],
                    'row_attr' => [ -- La 'div' qui contient le champ
                        'class' => 'my-row'
                    ]
                    'disabled' => false -- Le champ est affiché mais non modifiable, n'est pas à jour cette propriété lors de handleRequest
                */
                'help'  => '<small>Le titre maximum autorisés est 20</small>', /*
                    - Pour ajouter un petit texte d'aide sous le champ et 'help_html' si on veut ajouter du 'HTML' dans le texte
                */
                'help_html' => true,
            ])
            ->add('slug', TextType::class, [
                'required' => false
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'query_builder' => function(UserRepository $userRepository) {
                    return $userRepository->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC'); /*
                        - Pour définir la méthode de récupération
                    */
                }
            ])
            /*
                -- Pour l'upload de fichier
                ->add('thumbnailFile', FileType::class, [ 
                    'mapped' => false, -- On ne mappe pas le champ dans les données qui sont internes à notre formulaire donc il ne vas pas utiliser les set et get dessus
                    'constrains' => [
                        new File([ -- Pour valider les fichiers
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/msword',
                            ],
                            'mimeTypesMessage' => 'Seuls les fichiers PDF ou Word sont autorisés.',
                        ])
                        -- Ou.. 'Image' qui étend de 'File' mais avec d'autres options pour valider les images
                        new Image([
                            'maxSize' => '2M',
                            'mimeTypes' => ['image/jpeg', 'image/png'],
                            'mimeTypesMessage' => 'Seules les images sont autorisées.',
                            'minWidth' => 200,
                            'maxWidth' => 4000,
                            'minHeight' => 1080,
                            'maxHeight' => 4000,
                            'allowPortrait' => false
                        ])
                    ],
                    'attr' => [
                        'accept' => 'image/jpeg', 'image/png', 'image/webp'
                    ]
                ])
            */
            # Le package 'VichUploader' --
            ->add('thumbnailFile', VichImageType::class, [ /*
                - On peut utiliser le 'VichImageType' offert par vich
            */
                'required' => false,
                'allow_delete' => true, /*
                    - Permet d'ajouter une case à cocher pour supprimer l'image existante
                */
                'download_uri' => true, /*
                    - !! afficher un lien de téléchargement de l'image existant
                */
                'download_label' => 'Télécharger l\'image',
                'delete_label' => 'Supprimer',
                'image_uri' => true /*
                    - !! un aperçu de l'image actuelle si c'est une image
                */
            ])
            # --
            ->add('imageCollections', CollectionType::class, [ /*
                - Pour un champ de collection d'image puis dans 'assets/controllers/image-collection_controllerjs'
            */
                'entry_type' => ImageFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => ['label' => false],
                'attr' => [
                    'data-controller' => 'image-collection', /*
                        - Va être lier à 'allow_add'
                    */
                    'data-image-collection-add-label-value' => 'Ajouter une image', /*
                        - !! au 'static' du '..ccontroller.js'
                    */
                    'data-image-collection-delete-label-value' => 'Supprimer' // --
                ]
            ])
            /*
                ->add('category', EntityType::class, [
                    'class' => Category::class,
                    'expanded' => true,
                    'choice_label' => 'name',
                    'autocomplete' => true

                    -- Pour ajouter une source pour autocomplete
                    'attr' => ['autocomplete' => 'off'],
                    'autocomplete' => true,
                    'autocomplete_url' => $options['autocomplete_url'] ?? '/autocomplete'
                ])
            */
            ->add('category', CategoryAutocompleteField::class)
            ->add('quantities', CollectionType::class, [ /*
                - Permet de d'avoir une collection de ce type
            */
                'entry_type' => QuantityFormType::class, /*
                    - Le nom de la classe qui va permettre de représenter chacune de ces classes
                */
                'allow_add' => true, /*
                    - Permet l'ajout de quantité, rajoute un attribut 'data-..' qui contient un code qui doit être traité en js '[form-collection_controller.js]'
                */
                'allow_delete' => true, /*
                    - Pour autoriser la supperssion
                */
                'by_reference' => false, /*
                    - On ne souhaite pas qu'il modifie la collection mais qu'il utilise les méthodes 'add' et 'remove'
                */
                'entry_options' => ['label' => false], /*
                    - Permet de supprimer le libelé de numérotation
                */
                'attr' => [
                    'data-controller' => 'form-collection', /*
                        - Pour le lier à 'allow_add'
                    */
                    'data-form-collection-add-label-value' => 'Ajouter un ingrédient', /*
                        - !! au 'static' du '..controller.js'
                    */
                    'data-form-collection-delete-label-value' => 'Supprimer' // --
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'empty_data' => '',
                'required' => false, /*
                    - Pour l'éditeur 'EasyMDE' sinon il y'aura une erreur 
                */
                'attr' => [
                    'data-easy' => null
                ]
            ])
            ->add('duration')
            ->add('save', SubmitType::class, [
                'label' => 'Modifier'
            ]);

            if($options['is_admin']) { /*
                - Pour faire un affichage conditionné
            */
            }

            $builder
                ->addEventListener(FormEvents::PRE_SUBMIT, $this->factory->autoSlug('title')) /*
                    - Pour envoyer un 'collable' a 'autoSlug' $this->autoSlug(...) .. La méthode 'PRE_SUBMIT' nous permet d'agir avant la soummission du formulaire
                */
                ->addEventListener(FormEvents::POST_SUBMIT, $this->factory->timestamps()) /*
                    - Le 'POST_SUBMIT' nous renvoi l'entité qui a été rempli par le formulaire
                */
            /*
                ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) { -- Permet de faire un traitement lors de la soumission
                    $data = $event->getData();
                    if (is_string($data['roles'])) {
                        $data['roles'] = [$data['roles']];
                    }
                    $event->setData($data);
                })
                ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) { -- !! vant le remplissage via le setter
                    $form = $event->getForm();
                    if($this->security->isGranted('ROLE_ADMIN')) {
                        $form->add();
                    }
                })
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'validation_groups' => ['Default', 'Extra'], /*
                - Pour définir les groupes de validations à utilisé
            */
            /*
                - 'action' => '/blog/new',
                - 'method' => 'POST'
            */
            'is_admin' => false, /*
                - Les options passés au formulaire dépuis le controller
            */
            /*
                'validation_groups' => function ($form) { -- Validation conditionnel
                    $options = $form->getConfig()->getOptions();
                    return $options['is_admin'] ? ['Default', 'louer'] : ['Default'];
                } #[Assert\NotBlank(groups: ['louer'])]
            */
            # Pour éviter de gérer le 'csrf'.. dans un controller on le fais ainsi lors du 'isSubmitted'
            /*
                'csrf_protection' => true,
                'csrf_field_name' => '_token' -- Le name du input _token
                'csrf_token_id' => 'user_item' -- L'identifiant
            */
        ]);
    }

    public function autoSlug(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        if(empty($data['slug'])) {
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger->slug($data['title']));
            $event->setData($data);
        }
    }

    public function attachTimestamps(PostSubmitEvent $event)
    {
        $data = $event->getData();
        if(!($data instanceof Recipe)) {
            return;
        }
        $data->setUpdatedAt(new \DateTimeImmutable());
        if(!$data->getId()) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }
}