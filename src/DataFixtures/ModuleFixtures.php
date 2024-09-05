<?php
// src/DataFixtures/SchoolFixtures.php

namespace App\DataFixtures;

use App\Entity\School;
use App\Entity\Training;
use App\Entity\Module;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory as FakerFactory;

class ModuleFixtures extends Fixture
{
    private $entities;

    public function load(ObjectManager $manager): void
    {

        $this->entities = [];

        $faker = FakerFactory::create();

        $schools = [];
        for ($i = 0; $i < 3; $i++) {
            $school = new School();
            $school->setName($faker->company());
            $school->setDescription($faker->catchPhrase());

            $manager->persist($school);
            $schools[] = $school; // keep schools in an array for random
        }

        $modules = [];
        for ($i = 0; $i < 6; $i++) {
            $module = new Module();
            $module->setName($faker->word());
            $module->setDescription($faker->sentence());

            $manager->persist($module);
            $modules[] = $module; // keep modules in an array for random
        }

        $trainings = [];
        for ($i = 0; $i < 6; $i++) {
            $training = new Training();
            $training->setName($faker->jobTitle());
            $training->setDescription($faker->sentence());

            $randomSchool = $schools[array_rand($schools)];
            $training->setSchool($randomSchool);

            $randomModules = (array)array_rand($modules, 3);
            foreach ($randomModules as $moduleIndex) {
                $training->addModule($modules[$moduleIndex]);
            }

            $manager->persist($training);
            $trainings[] = $training;
        }

        $manager->flush();
        $this->entities = array_merge($trainings, $modules, $schools);
    }

    public function getEntities(): array
    {
        return $this->entities;
    }
}
