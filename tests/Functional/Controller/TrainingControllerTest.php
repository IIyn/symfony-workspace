<?php

namespace Functional\Controller;

use App\Entity\Training;
use App\Entity\Module;
use Config\StandardTest;

class TrainingTest extends StandardTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetTrainingRoute(): void
    {
        $training = parent::getEntityManager()->getRepository(Training::class)->findOneBy([]);

        $crawler = parent::$client->request('GET', '/training/' . $training->getId());
        $this->assertSame(200, parent::$client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Formation : ' . $training->getName());
        $this->assertCount($training->getModules()->count(), $crawler->filter('body > ul li'));
    }

    public function testBasicSearch(): void
    {
        $crawler = parent::$client->request('GET', '/search_training');

        $em = parent::$kernel->getContainer()->get('doctrine')->getManager();
        $trainings = $em->getRepository(Training::class)->findAll();
        $modules = $em->getRepository(Module::class)->findAll();

        $this->assertEquals(200, parent::$client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Recherche de formation par module');
        $this->assertCount(count($trainings), $crawler->filter('tbody > tr'));

        $this->assertCount(count($modules) + 1, $crawler->filter('label'));
    }

    public function testSearchTraining(): void
    {
        $crawler = parent::$client->request('GET', '/search_training');

        $this->assertSame(200, parent::$client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Recherche de formation par module');
        $this->assertGreaterThanOrEqual(6, $crawler->filter('tbody tr')->count());
    }

    public function testSearchTrainingByModule(): void
    {
        $modules = parent::getEntityManager()->getRepository(Module::class)->findAll();
        $crawler = parent::$client->request('GET', '/search_training?modules[]=' . $modules[0]->getId());

        $this->assertSame(200, parent::$client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Recherche de formation par module');
        $this->assertCount($modules[0]->getTrainings()->count(), $crawler->filter('tbody tr'));
    }

    public function testSearchTrainingByAnyModule(): void
    {
        $modules = parent::getEntityManager()->getRepository(Module::class)->findAll();
        $crawler = parent::$client->request('GET', '/search_training?modules[]=' . $modules[0]->getId() . '&match_any_module=1');

        $this->assertSame(200, self::$client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Recherche de formation par module');
        $this->assertGreaterThanOrEqual(0, $crawler->filter('tbody tr')->count());
    }

    public function testManageTraining(): void
    {
        $training = parent::getEntityManager()->getRepository(Training::class)->findOneBy([]);
        $crawler = self::$client->request('GET', '/manage_training/' . $training->getId());
        $this->assertSame(200, self::$client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Manage Training: ' . $training->getName());
        $this->assertSelectorTextContains('p', $training->getDescription());
        $this->assertSelectorTextContains('h2', 'Modules');

        $count = $training->getModules()->count();

        // should list all modules related to this training
        $this->assertCount($count, $crawler->filter('body > ul li'));
    }

    public function testDeleteTraining(): void
    {
        $training = parent::getEntityManager()->getRepository(Training::class)->findOneBy([]);
        // should remove the first module by submitting the form or link
        $module_to_remove = $training->getModules()->first();
        $count = $training->getModules()->count();
        $crawler = self::$client->request('GET', '/manage_training/' . $training->getId() . '?remove=' . $module_to_remove->getId());

        // should get -1 elements
        $this->assertCount($count - 1, $crawler->filter('body > ul li'));

        // should not have this module anymore
        $this->assertNotContains($module_to_remove->getName(), $crawler->filter('body > ul li'));
    }
}
