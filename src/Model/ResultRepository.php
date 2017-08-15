<?php

namespace Gietos\Kicker\Model;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

class ResultRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAll($limit = 10)
    {
        return $this->entityManager->getRepository(Result::class)->findBy([], ['playedAt' => 'DESC'], $limit);
    }

    public function getForPlayer(Player $player)
    {
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
               ORDER BY r.played_at DESC', $rsm)
            ->setParameter(1, $player->getId())
            ->setParameter(2, $player->getId())
        ;

        $results = $query->getResult();

        return $results;
    }
}
