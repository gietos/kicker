<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\EntityManagerInterface;
use Gietos\Kicker\Component\Alerts;
use Gietos\Kicker\Component\View;
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
        $this->response->headers->set('Content-Type', 'application/json');
        $view = $this->doRun($parameters);
        $view->setParam('alerts', $this->alerts->get());
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
