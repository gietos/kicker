<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\Query\Expr\OrderBy;
use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Model\PlayerRepository;
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
                $this->alerts->add('danger' , 'Winners and losers must not be empty');
            } else {
                try {
                    $winners = $this->entityManager->getRepository(Player::class)->findBy(['id' => $data['winners']]);
                    $losers = $this->entityManager->getRepository(Player::class)->findBy(['id' => $data['losers']]);
                    $result = new Result;
                    $result->setWinners($winners);
                    $result->setLosers($losers);
                    $result->setAddedBy($this->currentPlayer);

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

        if ($this->currentPlayer->getRole() == Player::ROLE_ADMIN) {
            $players = (new PlayerRepository($this->entityManager))->getAll();
        } else {
            $players = (new PlayerRepository($this->entityManager))->getOwnLeagues($this->currentPlayer);
        }

        $results = $this->entityManager->getRepository(Result::class)->findBy([], ['playedAt' => 'DESC'], 10);

        return $this->render('result/add.html.twig', compact('players', 'results'));
    }
}
