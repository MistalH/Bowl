<?php

use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testRollingAllZeros()
    {
        $game = new Game();

        // Rzutamy 20 razy 0 punktów
        for ($i = 0; $i < 20; $i++) {
            $game->roll(0);
        }

        // Oczekujemy, ¿e wynik koñcowy bêdzie wynosi³ 0
        $this->assertEquals(0, $game->getScore());
    }

    public function testRollingAllOnes()
    {
        $game = new Game();

        // Rzutamy 20 razy 1 punkt
        for ($i = 0; $i < 20; $i++) {
            $game->roll(1);
        }

        // Oczekujemy, ¿e wynik koñcowy bêdzie wynosi³ 20 (20 rzutów * 1 punkt)
        $this->assertEquals(20, $game->getScore());
    }
}

?>