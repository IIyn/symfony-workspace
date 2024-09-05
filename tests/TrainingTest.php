<?php

// namespace Functional\Controller;

// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
// use App\Entity\Training;
// use App\DataFixtures\ModuleFixtures;

// class TrainingTest extends WebTestCase
// {
//     private $entityManager;
//     protected static $client;
//     private $fixture;


//     public static function setUpBeforeClass(): void
//     {
//         self::$client = static::createClient();
//     }

//     protected function setUp(): void
//     {
//         $this->entityManager = self::$client->getContainer()
//             ->get('doctrine')
//             ->getManager();

//         $this->entityManager->getConnection()->beginTransaction();

//         // Load fixtures
//         $this->loadFixtures();
//     }
//     private function loadFixtures(): void

//     {
//         $this->fixture = new ModuleFixtures();
//         $this->fixture->load($this->entityManager);
//     }



//     protected function tearDown(): void
//     {
//         $this->entityManager->getConnection()->rollBack();
//         $this->entityManager = null; // Avoid memory leaks

//         // if ($this->entityManager) {
//         //     $this->entityManager->remove($this->fixture->getEntities());
//         //     $this->entityManager = null; // Avoid memory leaks
//         // }
//     }

//     public function testSearchTraining(): void
//     {

//         $crawler = self::$client->request('GET', '/search_training');

//         $this->assertSame(200, self::$client->getResponse()->getStatusCode());

//         $this->assertSelectorTextContains('h1', 'Recherche de formation par module');
//         $this->assertGreaterThanOrEqual(6, $crawler->filter('tbody tr')->count());
//     }
// }
