<?php

namespace Unit\Service;

use App\Service\Greetings;
use PHPUnit\Framework\TestCase;

class GreetingsTest extends TestCase
{
    public function testGreet()
    {
        $greetings = new Greetings();

        $this->assertInstanceOf(Greetings::class, $greetings);

        $this->assertEquals('Hello, Bob!', $greetings->greet('Bob'));
    }

    public function testBye()
    {
        $greetings = new Greetings();

        $this->assertInstanceOf(Greetings::class, $greetings);

        $this->assertEquals('Goodbye, Bob!', $greetings->bye('Bob'));
    }
}
