<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UserDTO
{
    /**
     * @Assert\Email
     */
    private $email;

    /**
     * @Assert\NotBlank
     */
    private $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, false);

        return new static($data->email, $data->password);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}