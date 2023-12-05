<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;
use Faker;

class CategorieFixtures extends Fixture
{
    public const FILM_CATEGORIE_REFERENCE = 'film-categorie';

    public function load(PersistenceObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        // on crée 10 catégories avec des noms, descriptions, notes
        for ($i = 0; $i < 10; $i++) {
            $categorie = new Categorie();
            $categorie->setNom($faker->name);

            $manager->persist($categorie);

            $this->addReference(self::FILM_CATEGORIE_REFERENCE . '_'. $i, $categorie);
        }

        $manager->flush();
    }
}