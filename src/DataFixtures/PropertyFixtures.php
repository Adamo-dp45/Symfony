<?php

namespace App\DataFixtures;

use App\Entity\Property;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PropertyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for($i = 1; $i < 100; $i++) {
            $property = new Property();
            $property
                ->setTitle($faker->words(3, true))
                ->setDescription($faker->sentences(3, true))
                ->setSurface($faker->numberBetween(20, 350))
                ->setRooms($faker->numberBetween(2, 10))
                ->setBedrooms($faker->numberBetween(1, 9))
                ->setFloor($faker->numberBetween(0, 15))
                ->setPrice($faker->numberBetween(100000, 1000000))
                ->setDescription($faker->text(20))
                ->setHeat($faker->numberBetween(0, count(Property::HEAD) - 1))
                ->setCity($faker->city())
                ->setAddress($faker->address())
                ->setPostalCode($faker->postcode())
                ->setSold(false)
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable())
            ;
            $manager->persist($property);
        }

        $manager->flush();
    }
}
