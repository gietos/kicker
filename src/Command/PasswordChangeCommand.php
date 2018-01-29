<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;

class PasswordChangeCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        /** @var Player $player */
        $player = $this->entityManager->find(Player::class, $this->session->get('player_id'));

        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['new_password']) || empty($data['old_password'])) {
                $this->alerts->add('danger', 'Both passwords must not be empty');
                $this->redirect('/password-change');
            }

            if (hash('sha256', $data['old_password']) !== $player->getPassword()) {
                $this->alerts->add('danger', 'Old password is wrong');
                $this->redirect('/password-change');
            }

            try {
                $player->setPassword($data['new_password']);
                $this->entityManager->merge($player);
                $this->entityManager->flush();

                $this->alerts->add('success', 'Password changed');
                $this->redirect('/');
            } catch (\Exception $e) {
                $this->alerts->add('danger', $e->getMessage());
                $this->redirect('/password-change');
            }
        }

        return $this->render('password-change.html.twig');
    }
}
