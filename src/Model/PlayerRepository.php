<?php

namespace Gietos\Kicker\Model;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\OrderBy;

class PlayerRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Player[]
     */
    public function getAll()
    {
        $players = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Player::class, 'p')
            ->andWhere('p.status = :statusActive')
            ->setParameter('statusActive', Player::STATUS_ACTIVE)
            ->addOrderBy(new OrderBy('p.mean - (3 * p.deviation)', 'DESC'))
            ->getQuery()
            ->getResult()
        ;

        return $players;
    }

    /**
     * @param Player $player
     * @return Player[]
     */
    public function getOwnLeagues(Player $player)
    {
        $leagues = (new LeagueRepository($this->entityManager))->getOwn($player);

        $leagues = array_merge($leagues, [$player->getLeague()]);

        $players = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Player::class, 'p')
            ->andWhere('p.league IN (:leagues)')
            ->setParameter('leagues', $leagues)
            ->addOrderBy(new OrderBy('p.mean - (3 * p.deviation)', 'DESC'))
            ->getQuery()
            ->getResult()
        ;

        return $players;
    }
}
