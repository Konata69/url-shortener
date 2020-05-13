<?php

namespace App\Tests\Service;

use App\DTO\UrlDTO;
use App\Entity\Url;
use App\Service\Shortener;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
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

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
    public function testShortExisted(): void
    {
        $urlStr = "http://www.example-existed.com";
        $hash = "abcdefgh";

        $dto = new UrlDTO($urlStr);

        $url = new Url($urlStr);
        $url->setHash($hash);

        $this->entityManager->persist($url);
        $this->entityManager->flush();
        $url = null;

        $this->shortener->short($dto);
        $url = $this->entityManager->getRepository(Url::class)->findOneBy(['url' => $urlStr]);

        $this->assertEquals($urlStr, $url->getUrl());
        $this->assertEquals($hash, $url->getHash());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testGetUrlByHash(): void
    {
        $urlStr = "http://www.example-existed.com";
        $hash = "abcdefgh";
        $hashNotExists = "abcdezxc";

        $url = new Url($urlStr);
        $url->setHash($hash);

        $this->entityManager->persist($url);
        $this->entityManager->flush();
        $url = null;

        $this->assertEquals($urlStr, $this->shortener->getUrlByHash($hash));
        $this->assertNull($this->shortener->getUrlByHash($hashNotExists));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
        $this->shortener = null;
    }
}
