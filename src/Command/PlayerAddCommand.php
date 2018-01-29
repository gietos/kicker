<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\League;
use Gietos\Kicker\Model\LeagueRepository;
use Gietos\Kicker\Model\Player;

class PlayerAddCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['name'])) {
                $this->alerts->add('danger', 'Name must not be empty');
                $this->redirect('/player/add');
            }

            try {
                $player = new Player;
                $player->setName($data['name']);
                $player->setPassword($data['password']);
                $player->setLeague($this->entityManager->find(League::class, $data['league']));
                $this->entityManager->persist($player);
                $this->entityManager->flush();

                $this->alerts->add('success', sprintf('Player <strong>%s</strong> added', $player->getName()));
                $this->redirect('/player/add');
            } catch (\Exception $e) {
                $this->alerts->add('danger', $e->getMessage());
                $this->redirect('/player/add');
            }
        }

        if ($this->currentPlayer->getRole() == Player::ROLE_ADMIN) {
            $leagues = (new LeagueRepository($this->entityManager))->getAll();
        } else {
            $leagues = (new LeagueRepository($this->entityManager))->getOwn($this->currentPlayer);
        }

        return $this->render('player/add.html.twig', compact('leagues'));
    }
}
