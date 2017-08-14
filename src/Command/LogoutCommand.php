<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;

class LogoutCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        $this->session->set('auth', false);
        $this->redirect('/login');

        return $this->render('action-completed.html.twig');
    }
}
