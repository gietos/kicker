<?php

namespace Gietos\Kicker\Command;

class IndexCommand extends AbstractCommand
{
    protected function doRun(array $parameters = [])
    {
        $this->response->headers->set('Content-type', 'text/html');
        $this->response->setContent($this->twig->render('index.html.twig', compact('messages')));
    }
}
