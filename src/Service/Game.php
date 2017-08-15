<?php

namespace Gietos\Kicker\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Gietos\Kicker\Model\Result;
use Moserware\Skills\GameInfo;
use Moserware\Skills\Player;
use Moserware\Skills\Rating;
use Moserware\Skills\Team;
use Moserware\Skills\Teams;
use Moserware\Skills\TrueSkill\FactorGraphTrueSkillCalculator;

class Game
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var FactorGraphTrueSkillCalculator
     */
    protected $calculator;

    /**
     * @var GameInfo
     */
    protected $gameInfo;

    public function __construct(EntityManagerInterface $entityManager, FactorGraphTrueSkillCalculator $calculator, GameInfo $gameInfo)
    {
        $this->entityManager = $entityManager;
        $this->calculator = $calculator;
        $this->gameInfo = $gameInfo;
    }

    public function getRating(\Gietos\Kicker\Model\Player $player)
    {
        return new Rating($player->getMean(), $player->getDeviation());
    }

    public function getRatings(Result $result)
    {
        /** @var \Gietos\Kicker\Model\Player[] $players */
        $players = [];

        $winnerTeam = new Team;
        foreach ($result->getWinners() as $winner) {
            $players[$winner->getId()] = $winner;
            $winnerTeam->addPlayer(new Player($winner->getId()), $this->getRating($winner));
        }

        $loserTeam = new Team;
        foreach ($result->getLosers() as $loser) {
            $players[$loser->getId()] = $loser;
            $loserTeam->addPlayer(new Player($loser->getId()), $this->getRating($loser));
        }

        $teams = Teams::concat($winnerTeam, $loserTeam);

        $ratingContainer = $this->calculator->calculateNewRatings($this->gameInfo, $teams, [0, 1]);

        foreach ($ratingContainer->getAllPlayers() as $skillPlayer) {
            /** @var Player $skillPlayer */
            $player = $players[$skillPlayer->getId()];
            /** @var Rating $rating */
            $rating = $ratingContainer->getRating($skillPlayer);
            $player->setMean($rating->getMean());
            $player->setDeviation($rating->getStandardDeviation());
        }

        return $players;
    }

    public function getWins(\Gietos\Kicker\Model\Player $player)
    {
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('wins', 'wins');
        $query = $this->entityManager->createNativeQuery('SELECT COUNT(*) wins FROM result_winner WHERE player_id = ?', $rsm);
        $query->setParameter(1, $player->getId());

        $counts = $query->getResult();
        if (count($counts) !== 1) {
            return 0;
        }

        return $counts[0]['wins'];
    }

    public function getLosses(\Gietos\Kicker\Model\Player $player)
    {
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('losses', 'losses');
        $query = $this->entityManager->createNativeQuery('SELECT COUNT(*) losses FROM result_loser WHERE player_id = ?', $rsm);
        $query->setParameter(1, $player->getId());

        $counts = $query->getResult();
        if (count($counts) !== 1) {
            return 0;
        }

        return $counts[0]['losses'];
    }
}
