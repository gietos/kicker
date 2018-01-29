<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\EntityManagerInterface;
use Gietos\Kicker\Component\Alerts;
use Gietos\Kicker\Component\View;
use Gietos\Kicker\Model\Player;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class AbstractCommand
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Alerts
     */
    protected $alerts;

    /**
     * @var Player
     */
    protected $currentPlayer;

    public function __construct(Request $request, Response $response, EntityManagerInterface $entityManager, \Twig_Environment $twig, Session $session)
    {
        $this->request = $request;
        $this->response = $response;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->session = $session;
        $this->alerts = new Alerts($session);
    }

    public function run(array $parameters = [])
    {
        $this->session->start();
        $this->response->headers->set('Content-type', 'text/html');

        if ($this->session->get('auth', false) == false && !$this instanceof LoginCommand) {
            $this->redirect('/login');
        }

        $this->currentPlayer = $this->entityManager->find(Player::class, $this->session->get('player_id'));

        $view = $this->doRun($parameters);
        $view->setParam('alerts', $this->alerts->get());
        $view->setParam('currentPlayer', $this->currentPlayer);
        $this->response->setContent($view->render($this->twig));
    }

    abstract protected function doRun(array $parameters = []): View;

    protected function render(string $template, array $params = [])
    {
        return new View($template, $params);
    }

    protected function redirect(string $url, int $code = 302)
    {
        $this->response->setStatusCode($code);
        $this->response->headers->set('Location', $url);
        $this->response->setContent('Redirect to ' . $url);
        $this->response->send();
        exit;
    }
}
