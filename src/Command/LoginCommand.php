<?php

namespace Gietos\Kicker\Command;

use Gietos\Kicker\Component\View;

class LoginCommand extends AbstractCommand
{
    protected function doRun(array $parameters = []): View
    {
        if ($this->session->get('auth', false) == true) {
            $this->redirect('/');
        }

        $data = $this->request->request->all();
        if (!empty($data)) {
            if (empty($data['secret'])) {
                $this->alerts->add('danger', 'Secret must not be empty');
                $this->redirect('/login');
            }

            if ($data['secret'] !== $_ENV['SECRET']) {
                $this->alerts->add('danger', 'Access denied');
                $this->redirect('/login');
            } else {
                $this->session->set('auth', true);
                $this->alerts->add('success', 'Access granted');
                $this->redirect('/');
            }
        }

        return $this->render('login.html.twig');
    }
}
