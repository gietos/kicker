<?php

namespace Gietos\Kicker\Model;

use Doctrine\ORM\EntityManagerInterface;

class LeagueRepository
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
     * @return League[]
     */
    public function getAll()
    {
        return $this->entityManager->getRepository(League::class)->findBy([], ['name' => 'ASC']);
    }

    /**
     * @param Player $player
     * @return League[]
     */
    public function getOwn(Player $player)
    {
        return $this->entityManager->createQueryBuilder()
            ->select('l')
            ->from(League::class, 'l')
            ->andWhere('l.owner = :owner')
            ->setParameter('owner', $player)
            ->getQuery()
            ->getResult()
        ;
    }
}
