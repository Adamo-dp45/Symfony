<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $categories = ['Short', 'Portable'];
        foreach($categories as $c) {
            $category = new Category();
            $category
                ->setName($c)
                ->setSlug(strtolower($c))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
            ;
            $manager->persist($category);
            $this->addReference($c, $category);
        }

        for($i = 1; $i < 30; $i++) {
            $product = new Product();
            $product
                ->setName($faker->name())
                ->setPrice($faker->numberBetween(100, 50000))
                ->setDescription($faker->text(20))
                ->setContent($faker->text())
                ->setImage($faker->image())
                ->setPromo($faker->boolean())
                ->addCategory($this->getReference($faker->randomElement($categories), Category::class))
            ;
            $manager->persist($product);
        }

        $manager->flush();
    }

}
