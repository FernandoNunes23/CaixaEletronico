<?php

require __DIR__ . '/vendor/autoload.php';

$config = require "config.php";

$app = new Ahc\Cli\Application('Caixa Eletrônico', '0.0.1');

/**
 * Inicialização das dependências, como a aplicação é simples e no momento possui somente um comando,
 * decidi não utilizar PSR-11 (Container) para realizar a injeção de dependência
 */
$colorText = new \Ahc\Cli\Output\Color();
$caixaEletronico = new Application\Domain\Entity\CaixaEletronico($config["notas_disponiveis"]);
$logger = new Monolog\Logger("application");
$logger->pushHandler(new \Monolog\Handler\StreamHandler("logs/application.log"));

/**
 * Injeção das dependências dentro do comando de Sacar
 */
$app->add(new Application\Console\Command\SacarCommand($caixaEletronico, $colorText, $logger));

$app->handle($_SERVER['argv']);