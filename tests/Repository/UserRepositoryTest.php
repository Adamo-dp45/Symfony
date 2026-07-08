<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase; // Permet d'écrire des tests dans le contexte du kernel, Test Fonctionnels

final class UserRepositoryTest extends KernelTestCase { // Le kernel va me permettre de récupérer le Repository

    private UserRepository $userRepository;

    /**
     * Méthode est exécutée avant chaque test
     * @return void
     */
    protected function setUp(): void
    {
        self::bootKernel(); // Lancer le kernel Symfony
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    public function testFindUserByEmail(): void
    {
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $user = new User();
        $user->setUsername('test');
        $user->setEmail('test@example.com');
        $user->setPassword('password123');
        $entityManager->persist($user);
        $entityManager->flush();
        $foundUser = $this->userRepository->findOneBy(['email' => 'test@example.com']);
        $this->assertNotNull($foundUser);
        $this->assertEquals('test@example.com', $foundUser->getEmail());
    }

    public function testCount()
    {
        $kernel = self::bootKernel();
        $users = self::getContainer()->get(UserRepository::class)->count([]);
        $this->assertEquals(13, $users);
    }

}