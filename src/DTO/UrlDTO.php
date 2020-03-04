<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UrlDTO
{
    /**
     * @Assert\NotBlank
     * @Assert\Url
     */
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}