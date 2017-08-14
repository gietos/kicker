<?php

use Gietos\Kicker\Command\AbstractCommand;
use Gietos\Kicker\Command\IndexCommand;
use Gietos\Kicker\Command\ResultAddCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

require_once __DIR__ . '/../bootstrap.php';

$context = new RequestContext;
$request = Request::createFromGlobals();
$context->fromRequest($request);

$routes = new RouteCollection;

$routes->add('index', new Route('/', ['_command' => IndexCommand::class]));
$routes->add('result-add', new Route('/result/add', ['_command' => ResultAddCommand::class]));

$matcher = new UrlMatcher($routes, $context);

$parameters = $matcher->match($context->getPathInfo());
$commandClass = $parameters['_command'];

/** @var AbstractCommand $command */
$response = new Response;

$loader = new Twig_Loader_Filesystem([__DIR__ . '/../templates']);
$twig = new Twig_Environment($loader);

$command = new $commandClass($request, $response, $entityManager, $twig);
$command->run($parameters);
$response->send();
