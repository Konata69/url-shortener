<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Auth implements AuthInterface
{
    private $encoder;
    private $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->em = $em;
    }

    public function createUser(UserDTO $dto): User
    {
        $user = new User();
        $user->setEmail($dto->getEmail());
        $user->setPassword($this->encoder->encodePassword($user, $dto->getPassword()));

        //TODO Вынести запись в бд в репозиторий
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}