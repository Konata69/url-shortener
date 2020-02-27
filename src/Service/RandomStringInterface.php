<?php

namespace App\Service;

interface RandomStringInterface
{
    public function generate(int $length): string;
}