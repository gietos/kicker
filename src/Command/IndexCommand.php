<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\Query\Expr;
use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Model\Result;

class IndexCommand extends AbstractCommand
{
    protected function doRun(array $parameters = [])
    {
        $this->response->headers->set('Content-type', 'text/html');

        $qb = $this->entityManager->createQueryBuilder();
        $players = $qb->select('p')
            ->from(Player::class, 'p')
            ->addOrderBy(new Expr\OrderBy('p.mean - (3 * p.deviation)', 'DESC'))
            ->getQuery()
            ->getResult()
        ;

        $results = $this->entityManager->getRepository(Result::class)->findBy([], ['playedAt' => 'DESC'], 10);

        $this->response->setContent($this->twig->render('index.html.twig', compact('players', 'results')));
    }
}
