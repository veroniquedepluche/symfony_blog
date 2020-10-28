<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
		{
			$faker = Factory::create('fr_FR');

			for ($i = 0; $i < 10; $i++) {
				$category = (new Category())
					->setName($faker->unique()->word)
					->setDescription($faker->realText());

				$manager->persist($category);

				$reference = 'category_' . $i;
				$this->addReference($reference, $category);
			}

			$manager->flush();
		}

        $manager->flush();
    }
}
