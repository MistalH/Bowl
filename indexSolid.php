<?php
declare(strict_types=1);


interface ScoreCalculatorInterface
{
    public function calculateScore(array $rolls): int;
}

interface ConsoleRunnableGameInterface
{
    public function runGame(ConsoleRunnableGameInterface $game): void;
}

class StandardScoreCalculator implements ScoreCalculatorInterface
{
    public function calculateScore(array $rolls): int
    {
        $score = 0;
        $frameIndex = 0;

        for ($frame = 0; $frame < 10; $frame++) {
            if ($frameIndex < count($rolls)) {
                if ($rolls[$frameIndex] === 10) {
                    $score += 10 + $this->getStrikeBonus($frameIndex, $rolls);
                    $frameIndex++;
                } else if ($frameIndex + 1 < count($rolls) && $rolls[$frameIndex] + $rolls[$frameIndex + 1] === 10) {
                    $score += 10 + $this->getSpareBonus($frameIndex, $rolls);
                    $frameIndex += 2;
                } else {
                    $score += $this->getFrameScore($frameIndex, $rolls);
                    $frameIndex += 2;
                }
            } else {
                break;
            }
        }

        return $score;
    }

    private function getStrikeBonus($frameIndex, $rolls)
    {
        if (isset($rolls[$frameIndex + 1]) && isset($rolls[$frameIndex + 2])) {
            return $rolls[$frameIndex + 1] + $rolls[$frameIndex + 2];
        }
        return 0;
    }

    private function getSpareBonus($frameIndex, $rolls)
    {
        if (isset($rolls[$frameIndex + 2])) {
            return $rolls[$frameIndex + 2];
        }
        return 0;
    }

    private function getFrameScore($frameIndex, $rolls)
    {
        if (isset($rolls[$frameIndex]) && isset($rolls[$frameIndex + 1])) {
            return $rolls[$frameIndex] + $rolls[$frameIndex + 1];
        }
        return 0;
    }
}

class Game implements ConsoleRunnableGameInterface
{
    private ScoreCalculatorInterface $scoreCalculator;
    private array $rolls;

    public function __construct(ScoreCalculatorInterface $scoreCalculator)
    {
        $this->setScoreCalculator($scoreCalculator);
        $this->rolls = [];
    }

    public function roll($pins)
    {
        $this->rolls[] = $pins;
    }

    public function getScore(): int
    {
        return $this->getScoreCalculator()->calculateScore($this->rolls);
    }

    public function getRolls(): array
    {
        return $this->rolls;
    }

    public function getScoreCalculator(): ScoreCalculatorInterface
    {
        return $this->scoreCalculator;
    }

    public function setScoreCalculator($scoreCalculator)
    {
        $this->scoreCalculator = $scoreCalculator;
    }

    function runGame(ConsoleRunnableGameInterface $game): void
    {
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
        echo "Punkty zdobyte w każdej rundzie:\n";

        $rolls = $game->getRolls();

        for ($i = 0; $i < count($rolls); $i += 2) {
            $frame = ($i / 2) + 1;
            $roll1 = $rolls[$i];
            $roll2 = $rolls[$i + 1] ?? '';
            echo "Runda $frame: $roll1 $roll2 punktów\n";
        }

        $finalScore = $game->getScore();
        echo "Wynik końcowy: $finalScore punktów\n";
    }
}


class ConsoleGameRunner
{

    private ConsoleRunnableGameInterface $game;

    public function __construct(ConsoleRunnableGameInterface $game)
    {
        $this->game = $game;
    }

    /**
     * @return ConsoleRunnableGameInterface
     */
    public function getGame(): ConsoleRunnableGameInterface
    {
        return $this->game;
    }

    public function runGame(): void
    {
        $this->getGame()->runGame($this->game);
    }

}
do {
    $gameRunner = new ConsoleGameRunner(new Game(new StandardScoreCalculator()));
    $gameRunner->runGame();

    echo "Czy chcesz zagrać ponownie? (Tak/Nie): ";
    $playAgain = strtolower(trim(fgets(STDIN)));
} while ($playAgain === "tak");

echo "Dziękujemy za grę. Do widzenia!\n";
