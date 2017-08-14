<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Model\Player;

class PlayerViewCommand extends AbstractCommand
{
    protected function doRun(array $parameters = [])
    {
        $this->response->headers->set('Content-type', 'text/html');

        $id = $parameters['id'];

        $player = $this->entityManager->find(Player::class, $id);

        $this->response->setContent($this->twig->render('player/view.html.twig', compact('player')));
    }
}
