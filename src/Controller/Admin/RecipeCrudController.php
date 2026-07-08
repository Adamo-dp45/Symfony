<?php

namespace App\Controller\Admin;

use App\EasyAdmin\Type\EasyPictureFormType;
use App\Entity\Recipe;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Override;
use Symfony\Component\DomCrawler\Field\FormField;

class RecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), /*
                - 'hideOnForm()' pour éviter qu'il soit dans le formulaire..
            */
            TextField::new('title', 'titre'),
            TextEditorField::new('content', 'contenu'), /*
                - 'TextEditorField' !! avoir un éditeur de texte
            */
            SlugField::new('slug')->setTargetFieldName('title'), /*
                - 'setTargetFieldName()' !! indiquer que le 'slug' est basé sur la propriété 'title' de l'entité
            */
            IntegerField::new('duration', 'durée'),
            ImageField::new('thumbnail', 'image')
                ->setBasePath('images')
                ->setUploadDir('public/images')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
                ->hideOnIndex() /*
                    - 'setUploadedFileNamePattern()' !! le nommage du fichier
                    - 'hideOnIndex' !! cacher sur le listing

                    - Pour l'upload de plusieurs images on.. 'vich\uploader' puis la relation 'OneToMany'
                        - CollectionField::new('images')->setEntryType(EasyPictureFormType::class)
                */
            , /*
                - BooleanField::new('isAvailable', 'Disponible'),
                - MoneyField::new('price')->setCurrency('EUR')
                - AssociationField::new('category', 'Catégorie') !! de fetch les catégories dans le select
                    ->autocomplete(..) -- On.. peut le configurer
                    ->setPreferredChoices(['10', '20']) -- !! les avoir en haut de la liste
            */
        ]; /*
            - Les colonnes à afficher
            - On.. personnaliser les champs selon la page 'index', 'edit', 'new' et 'show'
                if($pageName === Crud::PAGE_INDEX) {
                    return [
                        IdField::new('id'),
                        TextField::new('title'),
                        BooleanField::new('isPublished'),
                    ];
                }
        */
    }

    /**
     * Permet d'ajouter des filtres
     * @param Filters $filters
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('category')); /*
                - ->add(BooleanFilter::new('isAvailable'))
            */
    }

    /**
     * !! configurer les actions
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        $custom = Action::new('archive', 'Archiver')->linkToRoute('admin_post_archive', fn($entity) => ['id' => $entity->getId()]); /*
            - Pour ajouter une action personnalisée
        */
        return $actions
            ->add(Crud::PAGE_INDEX, $custom); /*
                ->remove(Crud::PAGE_INDEX, Action::DELETE) -- !! supprimer le bouton 'Supprimer' dans la liste
                ->remove(Crud::PAGE_INDEX, Action::EDIT) -- !! 'Modifier'..
                ->remove(Crud::PAGE_DETAIL, Action::EDIT) -- !! de la page de détail

                ->update(Crud::PAGE_INDEX, Action::DELETE, function(Action $action) {
                    return $action->displayIf(function ($entity) {
                        return !$entity->isProtected(); -- !! ne montré le bouton que si non protégé
                    });
                }) -- !! restreindre l'accès à certaines actions selon l'utilisateur ou l'entité
                ->update(Crud::PAGE_INDEX, Action::DELETE, static fn (Action $action) => $action->askConfirmation()) -- !! piloter le bouton de confirmation

                ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN') -- !! supprimer si super admin
                ->disable(Action::NEW, Action::EDIT); -- !! désactiver les actions créer et éditer partout
            */
    }

    #[Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Recette')
            ->setEntityLabelInPlural('Recettes'); /*
            - Pour configurer le label
        */
    }

    /**
     * !! modifier la requête par défaut de la liste
     */
    #[Override]
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $query = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters); /*
            - $query->andWhere('entity.isActive = true');
        */
        return $query;
    }

    #[Override]
    /**
     * !! remplir le champ 'createdAt' et 'updatedAt' automatiquement
     * @param Recipe $entityInstance
     * @return void
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setCreatedAt(new DateTimeImmutable()); /*
            - Ou.. utiliser un évènement de 'easyAdmin' via 'EasyAdminSubscriber' puis on implémente l'interface 'TimestampableInterface' sur l'entité
        */
        parent::persistEntity($entityManager, $entityInstance);
    }
}
