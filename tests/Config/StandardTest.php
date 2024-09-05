<?php

namespace Config;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\ModuleFixtures;

class StandardTest extends WebTestCase
{
    protected static $client;
    protected $entityManager;
    protected $fixture;

    public static function setUpBeforeClass(): void
    {
        if (!self::$client) {
            self::$client = static::createClient();
        }
    }

    protected function setUp(): void
    {
        $this->entityManager = self::$client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->entityManager->getConnection()->beginTransaction();

        // Load fixtures
        $this->loadFixtures();
    }

    private function loadFixtures(): void

    {
        $this->fixture = new ModuleFixtures();
        $this->fixture->load($this->entityManager);
    }

    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->getConnection()->rollBack();
        }
        $this->entityManager = null; // Avoid memory leaks

        // if ($this->entityManager) {
        //     $this->entityManager->remove($this->fixture->getEntities());
        //     $this->entityManager = null; // Avoid memory leaks
        // }
    }

    public function testDefinedFixtures(): void
    {
        $this->assertNotEmpty($this->fixture->getEntities());
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function getFixture()
    {
        return $this->fixture;
    }
}
