<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\Query\Expr\OrderBy;
use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Service\Game;
use Moserware\Skills\GameInfo;
use Moserware\Skills\TrueSkill\FactorGraphTrueSkillCalculator;

class MatchCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $match = null;
        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['players'])) {
                $this->alerts->add('danger', 'Players must not be empty');
            } elseif(count($data['players']) != 4) {
                $this->alerts->add('danger', '4 players must be selected');
            } else {
                try {
                    $game = new Game($this->entityManager, new FactorGraphTrueSkillCalculator, new GameInfo);
                    $match = $game->getCombinations($data['players']);
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
