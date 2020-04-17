<?php

require __DIR__ . '/vendor/autoload.php';

$config = require "config.php";

$app = new Ahc\Cli\Application('Caixa Eletrônico', '0.0.1');

$colorText = new \Ahc\Cli\Output\Color();
$caixaEletronico = new Application\Domain\Entity\CaixaEletronico($config["notas_disponiveis"]);
$logger = new Monolog\Logger("application");
$logger->pushHandler(new \Monolog\Handler\StreamHandler("logs/application.log"));

$app->add(new Application\Console\Command\SacarCommand($caixaEletronico, $colorText, $logger));

$app->handle($_SERVER['argv']);