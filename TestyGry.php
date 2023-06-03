<?php

use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testRollingAllZeros()
    {
        $game = new Game();

        // Rzutamy 20 razy 0 punkt�w
        for ($i = 0; $i < 20; $i++) {
            $game->roll(0);
        }

        // Oczekujemy, �e wynik ko�cowy b�dzie wynosi� 0
        $this->assertEquals(0, $game->getScore());
    }

    public function testRollingAllOnes()
    {
        $game = new Game();

        // Rzutamy 20 razy 1 punkt
        for ($i = 0; $i < 20; $i++) {
            $game->roll(1);
        }

        // Oczekujemy, �e wynik ko�cowy b�dzie wynosi� 20 (20 rzut�w * 1 punkt)
        $this->assertEquals(20, $game->getScore());
    }
}

?>