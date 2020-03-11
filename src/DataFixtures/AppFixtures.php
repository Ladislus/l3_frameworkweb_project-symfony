<?php

namespace App\DataFixtures;

use App\Entity\Cours;
use App\Entity\Semestre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;

class AppFixtures extends Fixture {

    public function load($manager) {

        $faker = Factory::create('fr_FR');

        $number_semestre = 6;

        for($i = 0; $i < $number_semestre; ++$i) {
            $semestre = new Semestre();
            $semestre->setName("Semestre ".($i + 1))->setPathway("INGÉ IMIS");
            $manager->persist($semestre);
        }
        $manager->flush();

        $semestre_repository = $manager->getRepository(Semestre::class);
        for($i = 1; $i <= 100; ++$i) {
            $cours = new Cours();
            $cours->setName($faker->colorName)
                ->setDescription($faker->text)
                ->setSemestre($semestre_repository->find(($i % $number_semestre) + 1));
            $manager->persist($cours);
        }
        $manager->flush();
    }
}
