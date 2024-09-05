<?php

namespace Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Training;
use App\DataFixtures\ModuleFixtures;
use Config\StandardTest;

class SchoolTest extends StandardTest
{

    protected function setUp(): void
    {
        parent::setUp();
    }


    protected function tearDown(): void
    {
        parent::tearDown();
    }


    public function testTrainingHasModules(): void
    {
        $training = parent::getEntityManager()->getRepository(Training::class)->findOneBy([]);
        $this->assertCount($training->getModules()->count(), $training->getModules());
    }
}
