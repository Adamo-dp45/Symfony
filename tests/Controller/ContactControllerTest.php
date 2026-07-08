<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContactControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient(); // Simuler un navigateur
        $client->request('GET', '/fr/contact');
        self::assertResponseIsSuccessful(); // 200 OK
        // self::assertSelectorTextContains('h1', 'Nous contactez');
        // self::assertResponseStatusCodeSame(Response::HTTP_OK);
        // self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED); Pour la permission
    }

    public function testSendContactWithInvalidEmail()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/contact');
        $form = $crawler->selectButton('Envoyer')->form([
            'username' => 'John',
            'email' => 'john@gmail.com',
            'message' => 'lorem'
        ]);
        $client->submit($form);
        $this->assertSelectorTextContains('.form-error-message', 'This value is not a valid email address.');
    }

}
