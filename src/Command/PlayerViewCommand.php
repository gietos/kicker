<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\Query\ResultSetMapping;
use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Model\Result;
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

        $rsm = new ResultSetMapping;
        $rsm->addEntityResult(Result::class, 'r');
        $rsm->addFieldResult('r', 'id', 'id');
        $rsm->addFieldResult('r', 'played_at', 'playedAt');
        $query = $this->entityManager
            ->createNativeQuery('SELECT r.* FROM (SELECT rl.result_id id
               FROM result_loser rl
               WHERE rl.player_id = ?
               UNION SELECT rw.result_id id
                     FROM result_winner rw
                     WHERE rw.player_id = ?
                      ) rp
                JOIN result r ON r.id = rp.id
                ', $rsm)
            ->setParameter(1, $player->getId());
        $results = $query->getResult();

        return $this->render('player/view.html.twig', compact('player', 'gamesCount', 'winRate', 'results'));
    }
}
