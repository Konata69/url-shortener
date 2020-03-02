<?php

namespace App\View;

use App\Entity\User;

class UserView
{
    private $email;

    public static function fromEntity(User $user): self
    {
        $userView = new self();
        $userView->email = $user->getEmail();

        return $userView;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function asArray(): array
    {
        return ['email' => $this->getEmail()];
    }
}