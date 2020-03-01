<?php

namespace App\Service;

use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class Shortener
{
    private $em;
    private $rep;
    private $rand;

    public function __construct(EntityManagerInterface $em, RandomStringInterface $rand)
    {
        $this->em = $em;
        $this->rep = $em->getRepository(Url::class);
        $this->rand = $rand;
    }

    /**
     * @param string $url
     * @return Url
     * @throws Exception
     */
    public function short(string $url): Url
    {
        $urlEntity = $this->rep->findOneBy(['url' => $url]);

        if ($urlEntity) {
            return $urlEntity;
        }

        $hash = $this->generateHash();

        $urlEntity = new Url($url);
        $urlEntity->setHash($hash);
        $this->em->persist($urlEntity);
        $this->em->flush();

        return $urlEntity;
    }

    //TODO Переместить в репозиторий
    public function getUrlByHash($hash): ?string
    {
        $urlEntity = $this->rep->findOneBy(['hash' => $hash]);

        if ($urlEntity) {
            return $urlEntity->getUrl();
        }

        return null;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function generateHash(): string
    {
        do {
            $hash = $this->rand->generate(8);
            $urlEntity = $this->rep->findOneBy(['hash' => $hash]);
        } while ($urlEntity !== null);

        return $hash;
    }
}