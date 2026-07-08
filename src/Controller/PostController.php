<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;

final class PostController extends AbstractController
{
    public function __construct(
        private Registry $registry,
        private WorkflowInterface $blogPublishingStateMachine, // Le nommage est important sinon symfony ne pourra pas l'injecter - ..StateMachine car c'est le mode qu'on a utiliser ou Workflow dans le cas contraire

        // private WorkflowInterface $blogPublishingWorkflow,
        /*
            Pour l'utiliser, dans workflow.yaml type: 'workflow' - Dans Post - private array $currentState = [] On peut laisser vide ou ['draft' => 1]
            Permet d'être dans plusieurs états à la fois Ex : transitions - to_review - to: [reviewed, draft], dans le from on peut dire [draft, published]
        */
    )
    {
    }

    #[Route('/articles', name: 'post')]
    public function workflow(): Response
    {
        $post = new Post();
        // $workflow = $this->registry->get($post, 'blog_publishing'); -- En premier param l'entité et en deuxième le nom de notre workflow - Ou utiliser le $blogPublishingStateMachine
        // $workflow->getEnabledTransitions($post); -- L'ensemble des transitions disponible sur notre workflow
        // $workflow->can($post, 'publish'); -- Vérifier si une transition est autorisé, Me donne false car actu on n'est en draft alors que pour publier il faut être en reviewed
        // $workflow->apply($post, 'publish') -- Appliquer une transition, Ici on aura une Exception car on n'est en draft, si j'utilise 'to_review' cela va fonctionner et si dd($post) on verra que son état à changer à reviewed

        $this->blogPublishingStateMachine->apply($post, 'to_review');
        try { // A commenter si on veut tester le metadata
            $this->blogPublishingStateMachine->apply($post, 'publish');
        } catch(NotEnabledTransitionException $e) { // Cette exception nous dit que cette transition n'est pas active
            // $e->getTransitionBlockerList(); -- Renvoi une liste des éléments qui ont bloqué la tansition 
        }

        return $this->render('post/index.html.twig', [
            'post' => $post
        ]);
    }
}
