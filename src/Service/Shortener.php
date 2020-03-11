<?php

namespace App\Service;

use App\DTO\UrlDTO;
use App\Entity\Url;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Shortener
{
    private $em;
    private $rep;
    private $rand;
    private $router;

    public function __construct(EntityManagerInterface $em, RandomStringInterface $rand, UrlGeneratorInterface $router)
    {
        $this->em = $em;
        $this->rep = $em->getRepository(Url::class);
        $this->rand = $rand;
        $this->router = $router;
    }

    /**
     * @param UrlDTO $dto
     * @return Url
     * @throws Exception
     */
    public function short(UrlDTO $dto): Url
    {
        $urlEntity = $this->rep->findOneBy(['url' => $dto->getUrl()]);

        if ($urlEntity) {
            return $urlEntity;
        }

        $hash = $this->generateHash();

        $urlEntity = new Url($dto->getUrl());
        $urlEntity->setUser($dto->getUser());
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

    public function getFollowUrl($hash): string
    {
        return $this->router->generate('follow', ['hash' => $hash], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param int $id
     * @param User $user
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    public function delete(int $id, User $user): void
    {
        $url = $this->rep->find($id);

        if ($url === null) {
            throw new EntityNotFoundException('Url not found');
        }

        if (!$user->hasUrl($url)) {
            throw new AccessDeniedException('User not allowed to delete this url');
        }

        $this->em->remove($url);
        $this->em->flush();
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