<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase; // Permet de tester les controllers

final class RecipeControllerTest extends WebTestCase
{
    // Fichier de configuration - phpunit.xml

    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/recipe');
        self::assertResponseIsSuccessful();
    }

    // createClient() - Créer un navigateur virtuel
    // request('METHOD', '/url') - Fait une requête HTTP
    // assertResponseIsSuccessful() - Vérifier si le code de retour est entre 200 et 299
    // assertResponseStatusCodeSame(302) - Vérifier un code HTTP précis
    // assertSelectorTextContains('body', 'Bonjour Symfony!') - Vérifier si un élément HTML contient un texte
    // assertRouteSame('nom_de_route') - Vérifier que la route active correspond
}
