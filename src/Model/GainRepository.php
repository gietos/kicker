<?php

namespace Gietos\Kicker\Model;

use Doctrine\ORM\EntityManagerInterface;

class GainRepository
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
     * @param Result[] $results
     * @return array
     */
    public function getMapForResults(array $results)
    {
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

        return $gainMap;
    }
}
