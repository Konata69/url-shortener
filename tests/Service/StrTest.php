<?php

namespace App\Tests\Service;

use App\Service\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    public function testGenerate(): void
    {
        $lengthDefault = 16;
        $code = (new Str())->generate();
        $this->assertEquals($lengthDefault, strlen($code));
        $this->assertRegExp('/[0-9a-zA-Z]{'.$lengthDefault.'}/', $code);
    }

    public function testGenerateCustomLength(): void
    {
        $length = 24;
        $code = (new Str())->generate($length);
        $this->assertEquals($length, strlen($code));
        $this->assertRegExp('/[0-9a-zA-Z]{'.$length.'}/', $code);
    }
}
