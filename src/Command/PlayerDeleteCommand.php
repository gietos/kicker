<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;

class PlayerDeleteCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['id'])) {
                throw new \Exception('Bad request: id is missing');
            }

            $player = $this->entityManager->find(Player::class, $data['id']);
            $player->setStatus(Player::STATUS_DELETED);
            $this->entityManager->flush();

            $this->alerts->add('success', 'Player deleted');
            return $this->render('action-completed.html.twig');
        }

        $this->alerts->add('danger', 'Bad request');
        return $this->render('action-completed.html.twig');
    }
}
