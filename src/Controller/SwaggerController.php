<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;

class SwaggerController extends AbstractController {

    /**
     * @OA\Get(
     *      path="/posts",
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Le nombre d'article à récupérer",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Mes articles",
     *          @OA\JsonContent(
     *              type="string",
     *              description="Nosarticles"
     *          ),
     *      )
     * )
     */
    public function index()
    {
        // @OA\XmlContent() -- Si on veut renvoyer du xml
    }

    /**
     * @OA\Get(
     *      path="/posts/{id}",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="L'id de l'article",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="L'article",
     *          @OA\JsonContent(
     *              type="string",
     *              description="Nosarticles"
     *          ),
     *      )
     * )
     */
    public function show()
    {
        // Parameter -- Permet d'indiquer comment fonctionne les paramètres
    }

        /**
     * @OA\Get(
     *      path="/posts/{id}",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="L'id de l'article",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="L'article",
     *          @OA\JsonContent(
     *              type="string",
     *              description="Nosarticles"
     *          ),
     *      )
     * )
     */
    public function update()
    {
    }

}