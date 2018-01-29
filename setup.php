<?php

use Gietos\Kicker\Model\Player;

require __DIR__ . '/bootstrap.php';

$player = new Player();
$player->setName('admin');
$player->setPassword('admin');
$player->setRole(Player::ROLE_ADMIN);
$entityManager->persist($player);
$entityManager->flush();

echo 'User: admin, password: admin' . PHP_EOL;
