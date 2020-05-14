<?php

namespace App\DataFixtures;

use App\Entity\Url;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("email@example.com");
        $user->setPassword("password");
        $manager->persist($user);

        $url = new Url("http://example-user.com");
        $url->setHash("exampleu");
        $url->setUser($user);
        $manager->persist($url);

        $manager->flush();
    }
}
