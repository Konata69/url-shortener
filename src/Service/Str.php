<?php

namespace App\Service;

class Str implements RandomStringInterface
{
    public function generate(int $length = 16): string
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $input_length = strlen($permitted_chars);
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $random_character = $permitted_chars[random_int(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }
}