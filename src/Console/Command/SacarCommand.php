<?php

namespace Application\Console\Command;

use Ahc\Cli\Input\Command;
use Ahc\Cli\Output\Color;
use Application\Domain\Entity\CaixaEletronico;
use Monolog\Logger;

/**
 * Class SacarCommand
 * @package Application\Console\Command
 */
class SacarCommand extends Command
{
    /** @var CaixaEletronico */
    private $caixaEletronico;

    /** @var Color */
    private $colorText;

    /** @var Logger */
    private $logger;

    /**
     * SacarCommand constructor.
     *
     * @param CaixaEletronico $caixaEletronico
     * @param Color $colorText
     * @param Logger $logger
     */
    public function __construct(
        CaixaEletronico $caixaEletronico,
        Color $colorText,
        Logger $logger
    )
    {
        parent::__construct('sacar', 'Ação de sacar.');

        $this
            ->argument('valor', 'Valor a ser sacado.')
            ->option("--json", "Formata o retorno para json.", 'boolval', false)
            ->usage(
                '<bold>php $0</end><comment> 20 </end> <eol/>' .
                '<bold>php $0</end><comment> --json 20 </end> <eol/>'
            );

        $this->caixaEletronico = $caixaEletronico;
        $this->colorText       = $colorText;
        $this->logger          = $logger;
    }

    /**
     * @param $valor
     * @throws \Exception
     */
    public function execute($json, $valor)
    {
        $this->logger->info("Executando o comando de saque.",[
            "parametros" => $this->app()->argv()
        ]);

        $io = $this->app()->io();

        try {

            if (is_null($valor)) {
                throw new \InvalidArgumentException("O parametro 'valor' deve ser informado.");
            }

            $notas = $this->caixaEletronico->sacar((float) $valor);

            $io->write($this->formataRetorno($valor, $notas, $json), true);

            $this->logger->info("Saque realizado com sucesso.", [
                "notas" => $notas
            ]);

        } catch (\Exception $e) {
            $this->logger->error("Ocorreu um erro ao retornar as notas para saque.",[
                "erro" => $e->getMessage()
            ]);

            echo $this->colorText->error($this->formataRetornoErro($e->getMessage(), $json) . "\n");

            return;
        }
    }

    private function formataRetornoErro(string $message, $json = false)
    {
        if ($json) {
            return json_encode([
                "status"  => "error",
                "message" => $message
            ]);
        }

        return $message;
    }

    /**
     * @param string $valor
     * @param array $notas
     * @param bool $json
     * @return string
     */
    private function formataRetorno(string $valor, array $notas, $json = false): string
    {
        if ($json) {
            return json_encode([
                "status" => "ok",
                "notas"  => $notas
            ]);
        }

        $valor = number_format($valor, 2, ",", ".");

        $print = "Valor do Saque: R$ {$valor} \nNotas: \n";

        foreach ($notas as $nota) {
            $print .=  "{$nota["quantidade"]} nota(s) de R$ {$nota["nota"]} \n";
        }

        return $print;
    }
}