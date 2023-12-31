<?php

namespace App\DataFixtures;

use App\Entity\Film;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;
use Faker;

class FilmFixtures extends Fixture
{
    public function load(PersistenceObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        // on crée 100 films avec des noms, descriptions, notes
        for ($i = 0; $i < 100; $i++) {
            $films = new Film();
            $films->setNom($faker->name);
            $films->setDescription($faker->paragraph(rand(1,5)), true);
            $films->setDate($faker->dateTimeBetween('2021-01-01', 'now'));
            $films->setNote($faker->numberBetween(0,20));
            $films->addCategory($this->getReference(CategorieFixtures::FILM_CATEGORIE_REFERENCE . '_'. mt_rand(0,9)));

            $manager->persist($films);
        }

        $manager->flush();
    }
}