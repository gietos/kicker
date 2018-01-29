<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;

class LoginCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        if ($this->session->get('auth', false) == true) {
            $this->redirect('/');
        }

        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['secret']) || empty($data['player'])) {
                $this->alerts->add('danger', 'Player & secret must not be empty');
                $this->redirect('/login');
            }

            /** @var Player $player */
            $player = $this->entityManager->getRepository(Player::class)->findOneBy(['name' => $data['player']]);
            if (null === $player) {
                $this->alerts->add('danger', 'Player not found');
                $this->redirect('/login');
            }

            if (hash('sha256', $data['secret']) !== $player->getPassword()) {
                $this->alerts->add('danger', 'Wrong password');
                $this->redirect('/login');
            } else {
                $this->session->set('auth', true);
                $this->session->set('player_id', $player->getId());
                $this->alerts->add('success', 'Access granted');
                $this->redirect('/');
            }
        }

        return $this->render('login.html.twig');
    }
}
