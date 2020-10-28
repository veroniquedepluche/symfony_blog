<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 25; $i++) {
        	// Récuperer une catégorie de manière aléatoire
			$categoryReference = 'category_' . $faker->numberBetween(0,9);
			$category = $this->getReference($categoryReference);

			$article = (new Article())
				->setCategory($category)
				->setTitle($faker->catchPhrase)
				->setContent($faker->realText())
				->setPublishedAt($faker->optional()->dateTimeBetween('-1 year'));

			$manager->persist($article);
		}

        $manager->flush();
    }

	public function getDependencies()
	{
		return [
			CategoryFixtures::class
		];
	}
}
