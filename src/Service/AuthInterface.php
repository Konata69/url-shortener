<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;

interface AuthInterface
{
    public function createUser(UserDTO $dto): User;
}