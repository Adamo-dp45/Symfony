<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public const ADMIN = 'ADMIN_USER';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        // TagFactory::createMany(15);

        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
            ->setUsername('admin')
            ->setIsVerified(true)
            ->setEmail('admin@gmail.com')
            ->setPassword($this->passwordHasher->hashPassword($user, 'admin'))
            ->setApiToken('user_token')
            // ->setLoginAttempts(0)
            // ->setIsBlocked(false)
        ;
        $this->addReference(self::ADMIN, $user);
        $manager->persist($user);

        for($i = 1; $i <= 10; $i++) { // faker
            $user = new User();
            $user->setRoles([])
                ->setUsername('user' . $i)
                ->setIsVerified(true)
                ->setEmail('user' . $i . '@gmail.com')
                ->setPassword($this->passwordHasher->hashPassword($user, 'user'))
                ->setApiToken('user_token'. $i)
                // ->setLoginAttempts(0)
                // ->setIsBlocked(false)
            ;
            $manager->persist($user);
            $this->addReference('USER' . $i, $user);
        }

        $manager->flush();

        /*
            $faker->words(3, true)
            $faker->sentences(3, true)
            $faker->text(20)
            $faker->address()
        */
    }

    public static function getGroups(): array
    {
        return ['recipes'];
    }
}
