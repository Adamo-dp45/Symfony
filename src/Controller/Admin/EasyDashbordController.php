<?php

namespace App\Controller\Admin;

use App\Entity\Option;
use App\Entity\Recipe;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Override;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/easyadmin', routeName: 'easyadmin')]
#[IsGranted('ROLE_ADMIN')]
class EasyDashbordController extends AbstractDashboardController
{
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(RecipeCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig'); -- Pour afficher une vue
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin | Symfony');
            # ->setFaviconPath('favicon.ico')
            # ->renderContentMaximized();
            # ->renderSidebarMinimized();
    }

    /**
     * Permet de personnalisé le menu de la sidebar
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        // EasyAdmin 4.29 : linkToCrud('Label', 'icône', Entité::class) est déprécié, on pointe directement le CrudController
        yield MenuItem::linkTo(RecipeCrudController::class, 'Recette', 'fas fa-list');
        yield MenuItem::linkTo(OptionCrudController::class, 'Options', 'fas fa-list'); /*
            - yield MenuItem::linkToUrl('Google', 'fas fa-list', 'https://google.com'); -- Pour une url
        */
    }

    #[Override]
    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->renderContentMaximized(); /*
            - Permet de modifier les params par défaut
                - 'renderContentMaximized' l'interface s'étire sur tout l'espace
        */
    }
}
