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
        $this->logger->info("Executando o comando de saque.");

        $io = $this->app()->io();

        try {

            if (is_null($valor)) {
                echo $this->colorText->error("O parâmetro 'valor' deve ser informado.\n\n");
                $this->app()->showHelp();
                return;
            }

            $notas = $this->caixaEletronico->sacar((float) $valor);

            $io->write($this->formataRetorno($valor, $notas, $json), true);

        } catch (\Exception $e) {
            echo $this->colorText->warn($e->getMessage()."\n");

            return;
        }
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
            return json_encode($notas);
        }

        $valor = number_format($valor, 2, ",", ".");

        $print = "Valor do Saque: R$ {$valor} \nNotas: \n";

        foreach ($notas as $nota) {
            $print .=  "{$nota["quantidade"]} nota(s) de R$ {$nota["nota"]} \n";
        }

        return $print;
    }
}