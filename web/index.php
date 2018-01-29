<?php

use Gietos\Kicker\Command\AbstractCommand;
use Gietos\Kicker\Command\IndexCommand;
use Gietos\Kicker\Command\LeagueAddCommand;
use Gietos\Kicker\Command\LoginCommand;
use Gietos\Kicker\Command\LogoutCommand;
use Gietos\Kicker\Command\MatchCommand;
use Gietos\Kicker\Command\PasswordChangeCommand;
use Gietos\Kicker\Command\PlayerAddCommand;
use Gietos\Kicker\Command\PlayerDeleteCommand;
use Gietos\Kicker\Command\PlayerEditCommand;
use Gietos\Kicker\Command\PlayerViewCommand;
use Gietos\Kicker\Command\ResultAddCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
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
$routes->add('login', new Route('/login', ['_command' => LoginCommand::class]));
$routes->add('password-change', new Route('/password-change', ['_command' => PasswordChangeCommand::class]));
$routes->add('logout', new Route('/logout', ['_command' => LogoutCommand::class]));
$routes->add('player-add', new Route('/player/add', ['_command' => PlayerAddCommand::class]));
$routes->add('player-edit', new Route('/player/edit/{id}', ['_command' => PlayerEditCommand::class]));
$routes->add('player-delete', new Route('/player/delete', ['_command' => PlayerDeleteCommand::class]));
$routes->add('player-view', new Route('/player/{id}', ['_command' => PlayerViewCommand::class]));
$routes->add('result-add', new Route('/result/add', ['_command' => ResultAddCommand::class]));
$routes->add('league-add', new Route('/league/add', ['_command' => LeagueAddCommand::class]));
$routes->add('match', new Route('/match', ['_command' => MatchCommand::class]));

$matcher = new UrlMatcher($routes, $context);

$parameters = $matcher->match($context->getPathInfo());
$commandClass = $parameters['_command'];

/** @var AbstractCommand $command */
$response = new Response;

$loader = new Twig_Loader_Filesystem([__DIR__ . '/../templates']);
$twig = new Twig_Environment($loader);

$session = new Session(new NativeSessionStorage(['cookie_lifetime' => 3600 * 24 * 30]));

$command = new $commandClass($request, $response, $entityManager, $twig, $session);
$command->run($parameters);
$response->send();
