<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;
use Faker;

class CategorieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(PersistenceObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        // on crée 10 catégories avec des noms, descriptions, notes
        for ($i = 0; $i < 10; $i++) {
            $categorie = new Categorie();
            $categorie->setNom($faker->name);

            $categorie->addFilm($this->getReference(FilmFixtures::CATEGORIE_FILM_REFERENCE . '_'. mt_rand(0,49)));

            $manager->persist($categorie);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            FilmFixtures::class,
        ];
    }
}