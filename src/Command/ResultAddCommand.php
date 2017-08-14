<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Model\Player;

class ResultAddCommand extends AbstractCommand
{
    protected function doRun(array $parameters = [])
    {
        $this->response->headers->set('Content-type', 'text/html');

        $players = $this->entityManager->getRepository(Player::class)->findAll();

        $this->response->setContent($this->twig->render('result/add.html.twig', compact('players')));
    }
}
