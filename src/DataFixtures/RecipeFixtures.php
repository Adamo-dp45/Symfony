<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use FakerRestaurant\Provider\fr_FR\Restaurant;

class RecipeFixtures extends Fixture implements DependentFixtureInterface // Permt d'indiquer que notre fixures dépend d'un autre
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        $ingredients = array_map(fn(string $name) => (new Ingredient())
            ->setName($name)
            ->setSlug(strtolower($name)), [
                "Farine",
                "Sucre",
                "Oeufs",
                "Beure",
                "Lait",
                "Sel",
                "Oignons",
                "Tomate",
                "Jimjimbre"
            ]);

        $unit = [
            'kg',
            'g',
            'ml',
            'l',
            'cl',
            'dl',
            'verre'
        ];

        foreach($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

        $categories = ['starter', 'main', 'dessert', 'drink'];
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

        for($i = 1; $i < 10; $i++) {
            $title = $faker->foodName();
            $recipe = new Recipe();
            $recipe
                ->setTitle($title)
                ->setSlug(strtolower(str_replace(' ', '-', $title)))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setContent($faker->paragraphs(10, true))
                ->setDuration($faker->numberBetween(5, 50))
                ->setUser($this->getReference('USER' . $faker->numberBetween(1, 10), User::class))
                ->setCategory($this->getReference($faker->randomElement($categories), Category::class))
            ;

            foreach ($faker->randomElements($ingredients, $faker->numberBetween(2, 5)) as $ingredient) {
                $recipe->addQuantity((new Quantity())
                    ->setQuantity($faker->numberBetween(1, 250))
                    ->setUnit($faker->randomElement($unit))
                    ->setIngredient($ingredient)
                );
            }
            $manager->persist($recipe);
        }

        /*
            $faker->foodName(); -- A random Food Name
            $faker->beverageName(); -- A random Beverage Name
            $faker->dairyName(); -- A random Dairy Name
            $faker->vegetableName(); -- A random Vegetable Name
            $faker->fruitName(); -- A random Fruit Name
            $faker->meatName(); -- A random Meat Name
            $faker->sauceName(); -- A random Sauce Name
        */

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [AppFixtures::class];
    }

}
