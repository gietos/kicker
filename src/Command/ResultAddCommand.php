<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\Query\Expr\OrderBy;
use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Model\Result;
use Gietos\Kicker\Service\Game;
use Moserware\Skills\GameInfo;
use Moserware\Skills\TrueSkill\FactorGraphTrueSkillCalculator;

class ResultAddCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['winners']) || empty($data['losers'])) {
                $alerts[] = ['class' => 'danger', 'message' => 'Winners and losers must not be empty'];
            } else {
                try {
                    $winners = $this->entityManager->getRepository(Player::class)->findBy(['id' => $data['winners']]);
                    $losers = $this->entityManager->getRepository(Player::class)->findBy(['id' => $data['losers']]);
                    $result = new Result;
                    $result->setWinners($winners);
                    $result->setLosers($losers);

                    $game = new Game($this->entityManager, new FactorGraphTrueSkillCalculator, new GameInfo);
                    $players = $game->getRatings($result);

                    foreach ($players as $player) {
                        $this->entityManager->persist($player);
                    }
                    $this->entityManager->persist($result);
                    $this->entityManager->flush();

                    $this->alerts->add('success', 'Result added');
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

        $results = $this->entityManager->getRepository(Result::class)->findBy([], ['playedAt' => 'DESC'], 10);

        return $this->render('result/add.html.twig', compact('players', 'results'));
    }
}
