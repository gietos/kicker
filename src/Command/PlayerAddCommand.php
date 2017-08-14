<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;

class PlayerAddCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $this->response->headers->set('Content-type', 'text/html');

        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['name'])) {
                $this->alerts->add('danger', 'Name must not be empty');
                $this->redirect('/player/add');
            }

            try {
                $player = new Player;
                $player->setName($data['name']);
                $this->entityManager->persist($player);
                $this->entityManager->flush();

                $this->alerts->add('success', sprintf('Player <strong>%s</strong> added', $player->getName()));
                $this->redirect('/player/add');
            } catch (\Exception $e) {
                $this->alerts->add('danger', $e->getMessage());
                $this->redirect('/player/add');
            }
        }

        return $this->render('player/add.html.twig');
    }
}
