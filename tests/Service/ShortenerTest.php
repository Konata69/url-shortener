<?php

namespace App\Tests\Service;

use App\DTO\UrlDTO;
use App\Entity\Url;
use App\Service\Shortener;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShortenerTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Shortener
     */
    private $shortener;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->shortener = self::$container->get(Shortener::class);
    }

    public function testShort(): void
    {
        $urlStr = "http://www.example.com";
        $length = 8;
        $dto = new UrlDTO($urlStr);
        $this->shortener->short($dto);
        $url = $this->entityManager->getRepository(Url::class)->findOneBy(['url' => $urlStr]);

        $this->assertEquals($urlStr, $url->getUrl());
        $this->assertRegExp('/[0-9a-zA-Z]{'.$length.'}/', $url->getHash());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
        $this->shortener = null;
    }
}
