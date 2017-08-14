<?php

namespace Gietos\Kicker\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function __construct(Request $request, Response $response, EntityManagerInterface $entityManager, \Twig_Environment $twig)
    {
        $this->request = $request;
        $this->response = $response;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    public function run(array $parameters = [])
    {
        $this->response->headers->set('Content-Type', 'application/json');
        $this->doRun($parameters);
    }

    abstract protected function doRun(array $parameters = []);
}
