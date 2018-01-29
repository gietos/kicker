<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\League;
use Gietos\Kicker\Model\LeagueRepository;
use Gietos\Kicker\Model\Player;

class PlayerEditCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $id = $parameters['id'];

        /** @var Player $player */
        $player = $this->entityManager->find(Player::class, $id);

        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['name'])) {
                $this->alerts->add('danger', 'Name must not be empty');
                $this->redirect('/player/edit/' . $id);
            }

            try {
                $player->setName($data['name']);
                /** @var League $league */
                $league = $this->entityManager->find(League::class, $data['league']);
                $player->setLeague($league);

                if ($this->currentPlayer->getRole() == Player::ROLE_ADMIN && !empty($data['password'])) {
                    $player->setPassword($data['password']);
                    $this->alerts->add('info', sprintf('Password for player <strong>%s</strong> changed', $player->getName()));
                }

                $this->entityManager->persist($player);
                $this->entityManager->flush();

                $this->alerts->add('success', sprintf('Player <strong>%s</strong> saved', $player->getName()));
                $this->redirect('/player/' . $id);
            } catch (\Exception $e) {
                $this->alerts->add('danger', $e->getMessage());
                $this->redirect('/player/edit/' . $id);
            }
        }

        $leagues = (new LeagueRepository($this->entityManager))->getAll();

        return $this->render('player/edit.html.twig', compact('player', 'leagues'));
    }
}
