<?php

namespace App\Tests\EndToEnd;

use Symfony\Component\Panther\PantherTestCase;

class PantherTest extends PantherTestCase {

    public function testLoginFlow(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/fr/login');
        $this->assertSelectorExists('input[name="email"]');
        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'user1@gmail.com',
            'password' => 'user',
        ]);
        $client->submit($form);
        $client->waitFor('h1');
        $this->assertSelectorTextContains('h1', 'Tableau de bord');
    }

}

/*
| Méthode       | Support JS | Navigateur réel| Recommandé pour…                     |
| ------------- | ---------- | ---------------| -------------------------------------|
| WebTestCase   | No         | No             | Parcours simples, formulaire, route  |
| Panther       | ✅         | ✅            | E2E complet avec JS, animations, etc.|
*/