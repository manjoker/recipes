<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager): void
    {
        $ingredients = [];

        for ($i=1; $i < 50; $i++){
            $ingredient = new Ingredient();
            $ingredient
                ->setName($this->faker->word())
                ->setPrice(mt_rand(1, 100));
            $ingredients[] = $ingredient;  
            $manager->persist($ingredient);
        }
        
        for ($j=1; $j < 30; $j++) {
            $recipe = new Recipe();

            $recipe->setName($this->faker->word())
            ->setNbPeople(mt_rand(1, 20))
            ->setDifficulty(mt_rand(1,10))
            ->setTime($this->faker->time(mt_rand(1, 1441)))
            ->setDescription($this->faker->sentence(15))
            ->setPrice(mt_rand(10, 70))
            ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
            ;

            for ($k=1; $k < mt_rand(5, 15); $k++){
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients) -1)]);
            }
           
            $manager->persist($recipe);
        }
        $manager->flush();
    }
}
