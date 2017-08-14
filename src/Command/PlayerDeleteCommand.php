<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Model\Player;

class PlayerDeleteCommand extends AbstractCommand
{
    protected function doRun(array $parameters = [])
    {
        $this->response->headers->set('Content-type', 'text/html');

        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['id'])) {
                throw new \Exception('Bad request: id is missing');
            }

            $player = $this->entityManager->find(Player::class, $data['id']);
            $this->entityManager->remove($player);
            $this->entityManager->flush();
            $alerts[] = ['class' => 'success', 'message' => 'Player deleted'];
            $this->response->setContent($this->twig->render('action-completed.html.twig', compact('alerts')));
            return;
        }

        $alerts[] = ['class' => 'danger', 'message' => 'Bad request'];
        $this->response->setContent($this->twig->render('action-completed.html.twig', compact('alerts')));
    }
}
