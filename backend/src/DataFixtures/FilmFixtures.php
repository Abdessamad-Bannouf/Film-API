<?php

namespace App\DataFixtures;

use App\Entity\Film;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager as PersistenceObjectManager;
use Faker;

class FilmFixtures extends Fixture
{
    public const CATEGORIE_FILM_REFERENCE = 'categorie-film';

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

            $manager->persist($films);

            $this->addReference(self::CATEGORIE_FILM_REFERENCE . '_'. $i, $films);

        }

        $manager->flush();
    }
}