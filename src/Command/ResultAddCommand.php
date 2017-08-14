<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Model\Player;
use Gietos\Kicker\Model\Result;

class ResultAddCommand extends AbstractCommand
{
    protected function doRun(array $parameters = [])
    {
        $this->response->headers->set('Content-type', 'text/html');

        $players = $this->entityManager->getRepository(Player::class)->findAll();

        $results = $this->entityManager->getRepository(Result::class)->findBy([], ['playedAt' => 'DESC'], 10);

        $this->response->setContent($this->twig->render('result/add.html.twig', compact('players', 'results')));
    }
}
