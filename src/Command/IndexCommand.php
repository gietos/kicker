<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\Query\Expr\OrderBy;
use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Gain;
use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Model\Result;
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

        $results = $this->entityManager->getRepository(Result::class)->findBy([], ['playedAt' => 'DESC'], 10);

        $gains = $this->entityManager->createQueryBuilder()
            ->select('g')
            ->from(Gain::class, 'g')
            ->andWhere('g.result IN (:results)')
            ->setParameter('results', $results)
            ->getQuery()
            ->getResult()
        ;

        $gainMap = [];
        foreach ($gains as $gain) {
            /** @var Gain $gain */
            $gainMap[$gain->getResult()->getId()][$gain->getPlayer()->getId()] = $gain->getGain();
        }

        return $this->render('index.html.twig', compact('players', 'results', 'wins', 'losses', 'gainMap'));
    }
}
