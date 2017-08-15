<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\Query\Expr\OrderBy;
use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;

class MatchCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['players'])) {
                $alerts[] = ['class' => 'danger', 'message' => 'Players must not be empty'];
            } else {
                try {
                    // ... Match

                    $match = 'Match';
                } catch (\Exception $e) {
                    $this->alerts->add('danger', $e->getMessage());
                }
            }
        }

        $qb = $this->entityManager->createQueryBuilder();
        $players = $qb->select('p')
            ->from(Player::class, 'p')
            ->andWhere('p.status = :statusActive')
            ->setParameter('statusActive', Player::STATUS_ACTIVE)
            ->addOrderBy(new OrderBy('p.mean - (3 * p.deviation)', 'DESC'))
            ->getQuery()
            ->getResult()
        ;

        return $this->render('match.html.twig', compact('players', 'match'));
    }
}
