<?php

require __DIR__ . '/vendor/autoload.php';

$config = require "config.php";

if (empty($argv[1])) {
    echo "É necessário passar o valor como parâmetro. \n";
    exit();
}

$valor = (float) $argv[1];

$caixaEletronico = new \Application\Domain\Entity\CaixaEletronico($config["notas_disponiveis"]);

try {
    $notas = $caixaEletronico->sacar($valor);

    $valor = number_format($valor, 2 , ",", ".");

    $print = "Valor do Saque: R$ {$valor} \nNotas: \n";

    foreach ($notas as $nota) {
        $print .=  "{$nota["quantidade"]} nota(s) de R$ {$nota["nota"]} \n";
    }

    echo $print . "\n";

} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
}