<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\League;
use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Model\PlayerRepository;

class LeagueAddCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        /** @var Player $currentPlayer */
        $currentPlayer = $this->entityManager->find(Player::class, $this->session->get('player_id'));
        if ($currentPlayer->getRole() !== Player::ROLE_ADMIN) {
            $this->alerts->add('danger', 'Admin role needed');
            $this->redirect('/');
        }

        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['name'])) {
                $this->alerts->add('danger', 'Name must not be empty');
                $this->redirect('/league/add');
            }

            try {
                $league = new League;
                $league->setName($data['name']);
                /** @var Player $owner */
                $owner = $this->entityManager->find(Player::class, $data['owner']);
                $league->setOwner($owner);
                $players = $this->entityManager->getRepository(Player::class)->findBy(['id' => $data['players']]);
                $league->setPlayers($players);

                $this->entityManager->persist($league);
                $this->entityManager->flush();

                $this->alerts->add('success', sprintf('League <strong>%s</strong> added', $league->getName()));
                $this->redirect('/league/add');
            } catch (\Exception $e) {
                $this->alerts->add('danger', $e->getMessage());
                $this->redirect('/league/add');
            }
        }

        $players = (new PlayerRepository($this->entityManager))->getAll();

        return $this->render('league/add.html.twig', compact('players'));
    }
}
