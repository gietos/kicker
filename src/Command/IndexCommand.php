<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\Query\Expr;
use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Model\Result;

class IndexCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $qb = $this->entityManager->createQueryBuilder();
        $players = $qb->select('p')
            ->from(Player::class, 'p')
            ->andWhere('p.status = :statusActive')
            ->setParameter('statusActive', Player::STATUS_ACTIVE)
            ->addOrderBy(new Expr\OrderBy('p.mean - (3 * p.deviation)', 'DESC'))
            ->getQuery()
            ->getResult()
        ;

        $results = $this->entityManager->getRepository(Result::class)->findBy([], ['playedAt' => 'DESC'], 10);

        return $this->render('index.html.twig', compact('players', 'results'));
    }
}
