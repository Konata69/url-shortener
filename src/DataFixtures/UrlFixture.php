<?php

namespace App\DataFixtures;

use App\Entity\Url;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UrlFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $url = new Url("http://example.com");
        $url->setHash("abcdefghijklmnop");
        $manager->persist($url);

        $manager->flush();
    }
}
