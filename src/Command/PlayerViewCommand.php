<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;

class PlayerViewCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $this->response->headers->set('Content-type', 'text/html');

        $id = $parameters['id'];

        $player = $this->entityManager->find(Player::class, $id);

        return $this->render('player/view.html.twig', compact('player'));
    }
}
