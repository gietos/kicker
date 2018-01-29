<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\Query\Expr\OrderBy;
use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\GainRepository;
use Gietos\Kicker\Model\LeagueRepository;
use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Model\ResultRepository;
use Gietos\Kicker\Service\Game;
use Moserware\Skills\GameInfo;
use Moserware\Skills\TrueSkill\FactorGraphTrueSkillCalculator;

class IndexCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $qb = $this->entityManager->createQueryBuilder();
        $players = $qb->select('p')
            ->from(Player::class, 'p')
            ->andWhere('p.status = :statusActive')
            ->setParameter('statusActive', Player::STATUS_ACTIVE)
            ->addOrderBy(new OrderBy('p.mean - (3 * p.deviation)', 'DESC'))
            ->getQuery()
            ->getResult()
        ;

        $game = new Game($this->entityManager, new FactorGraphTrueSkillCalculator, new GameInfo);
        $wins = $losses = [];
        foreach ($players as $player) {
            /** @var Player $player */
            $wins[$player->getId()] = $game->getWins($player);
            $losses[$player->getId()] = $game->getLosses($player);
        }

        $results = (new ResultRepository($this->entityManager))->getAll();

        $leagues = (new LeagueRepository($this->entityManager))->getAll();

        $gainMap = (new GainRepository($this->entityManager))->getMapForResults($results);

        return $this->render('index.html.twig', compact('players', 'results', 'wins', 'losses', 'gainMap', 'leagues'));
    }
}
