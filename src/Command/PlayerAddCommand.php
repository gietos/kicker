<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Model\Player;

class PlayerAddCommand extends AbstractCommand
{
    protected function doRun(array $parameters = [])
    {
        $this->response->headers->set('Content-type', 'text/html');

        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['name'])) {
                $alerts[] = ['class' => 'danger', 'message' => 'Name must not be empty'];
                $this->response->setContent($this->twig->render('player/add.html.twig', compact('alerts')));
                return;
            }

            try {
                $player = new Player;
                $player->setName($data['name']);
                $this->entityManager->persist($player);
                $this->entityManager->flush();
                $alerts[] = ['class' => 'success', 'message' => 'Player added'];
                $this->response->setContent($this->twig->render('player/add.html.twig', compact('alerts')));
                return;
            } catch (\Exception $e) {
                $alerts[] = ['class' => 'danger', 'message' => $e->getMessage()];
                $this->response->setContent($this->twig->render('player/add.html.twig', compact('alerts')));
                return;
            }
        }

        $this->response->setContent($this->twig->render('player/add.html.twig'));
    }
}
