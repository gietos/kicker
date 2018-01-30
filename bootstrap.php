<?php

date_default_timezone_set('Europe/Moscow');

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require_once __DIR__ . '/vendor/autoload.php';

if (!file_exists(__DIR__ . '/.env')) {
    throw new \Exception('Environment file was not found');
}

$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

$dotenv = new Dotenv(__DIR__);
$dotenv->load();
$dotenv->required(['DB', 'SECRET'])->notEmpty();

$config = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/src/Model'], false, null, null, false);
$config->setProxyDir(sys_get_temp_dir());
$config->setAutoGenerateProxyClasses(true);
$conn = [
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/' . $_ENV['DB'],
];

$entityManager = EntityManager::create($conn, $config);
