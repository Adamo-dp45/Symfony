<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProfileControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/profile');
        self::assertResponseRedirects('/connexion');
    }

    public function testSuccessfullLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/connexion');
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'admin@gmail.com',
            'password' => 'admin'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/profile');
    }

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/connexion');
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'admin@gmail.com',
            'password' => 'fakepassword'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/fr/connexion'); // On n'attend une redirection
        $client->followRedirect(); // Suis la redirection
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testAdminCheck()
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@gmail.com']);

        $client->loginUser($user); // Permet d'authentifier un utilisateur
        $client->request('GET', '/fr/admin');

        $this->assertResponseIsSuccessful();
    }
}
