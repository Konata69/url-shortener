<?php

namespace App\DTO;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class UrlDTO
{
    /**
     * @Assert\NotBlank
     * @Assert\Url
     */
    private $url;

    private $user;

    public function __construct(string $url, UserInterface $user = null)
    {
        $this->url = $url;
        $this->user = $user;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}