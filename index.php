<?php

class Game
{
    public $score;
    public $rolls;
    public $currentRoll;

    public function __construct()
    {
        $this->score = 0;
        $this->rolls = array();
        $this->currentRoll = 0;
    }

    public function roll($pins)
    {
        $this->rolls[$this->currentRoll] = $pins;
        $this->currentRoll++;
    }

    public function getScore()
    {
        $score = 0;
        $frameIndex = 0;

        for ($frame = 0; $frame < 10; $frame++) {
            if ($frameIndex < count($this->rolls)) {
                if ($this->rolls[$frameIndex] === 10) {
                    $score += 10 + $this->getStrikeBonus($frameIndex);
                    $frameIndex++;
                } else if ($frameIndex + 1 < count($this->rolls) && $this->rolls[$frameIndex] + $this->rolls[$frameIndex + 1] === 10) {
                    $score += 10 + $this->getSpareBonus($frameIndex);
                    $frameIndex += 2;
                } else {
                    $score += $this->getFrameScore($frameIndex);
                    $frameIndex += 2;
                }
            } else {
                break;
            }
        }

        return $score;
    }

    private function getStrikeBonus($frameIndex)
    {
        if (isset($this->rolls[$frameIndex + 1]) && isset($this->rolls[$frameIndex + 2])) {
            return $this->rolls[$frameIndex + 1] + $this->rolls[$frameIndex + 2];
        }
        return 0;
    }

    private function getSpareBonus($frameIndex)
    {
        if (isset($this->rolls[$frameIndex + 2])) {
            return $this->rolls[$frameIndex + 2];
        }
        return 0;
    }

    private function getFrameScore($frameIndex)
    {
        if (isset($this->rolls[$frameIndex]) && isset($this->rolls[$frameIndex + 1])) {
            return $this->rolls[$frameIndex] + $this->rolls[$frameIndex + 1];
        }
        return 0;
    }
}

function playGame()
{
    $game = new Game();

    echo "Gra w kręgle\n";

    for ($frame = 1; $frame <= 10; $frame++) {
        echo "Runda $frame\n";

        do {
            echo "Rzut 1 - Podaj liczbę przewróconych kręgli (0-10): ";
            $pins1 = intval(readline());
        } while ($pins1 < 0 || $pins1 > 10);

        $game->roll($pins1);
        $score = $game->getScore();
        echo "Aktualna liczba punktów: $score\n";

        if ($pins1 === 10) {
            echo "Strike!\n";
            continue;
        }

        do {
            echo "Rzut 2 - Podaj liczbę przewróconych kręgli (0-" . (10 - $pins1) . "): ";
            $pins2 = intval(readline());
        } while ($pins2 < 0 || $pins2 > (10 - $pins1));

        $game->roll($pins2);
        $score = $game->getScore();
        echo "Aktualna liczba punktów: $score\n";

        if (($pins1 + $pins2) === 10) {
            echo "Spare!\n";
        }
    }

    echo "\nKoniec gry\n";
    $score = $game->getScore();
    echo "Punkty zdobyte w każdej rundzie:\n";

    $rolls = $game->rolls;

    for ($i = 0; $i < count($rolls); $i += 2) {
        $frame = ($i / 2) + 1;
        $roll1 = $rolls[$i];
        $roll2 = isset($rolls[$i + 1]) ? $rolls[$i + 1] : '';
        echo "Runda $frame: $roll1 $roll2 punktów\n";
    }

    $finalScore = $game->getScore();
    echo "Wynik końcowy: $finalScore punktów\n";
}

do {
    playGame();

    echo "Czy chcesz zagrać ponownie? (T/N): ";
    $choice = strtoupper(readline());
} while ($choice === "T");

echo "Dziękujemy za grę!";

?>
