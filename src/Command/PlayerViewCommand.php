<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Service\Game;
use Moserware\Skills\GameInfo;
use Moserware\Skills\TrueSkill\FactorGraphTrueSkillCalculator;

class PlayerViewCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $id = $parameters['id'];

        /** @var Player $player */
        $player = $this->entityManager->find(Player::class, $id);

        $game = new Game($this->entityManager, new FactorGraphTrueSkillCalculator, new GameInfo);
        $wins = $game->getWins($player);
        $losses = $game->getLosses($player);
        $gamesCount = $wins + $losses;
        $winRate = null;
        if ($wins != 0 && $gamesCount != 0) {
            $winRate = $wins / $gamesCount * 100;
        }

        return $this->render('player/view.html.twig', compact('player', 'gamesCount', 'winRate'));
    }
}
