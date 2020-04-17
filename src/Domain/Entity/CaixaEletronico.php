<?php

declare(strict_types=1);

namespace Application\Domain\Entity;

/**
 * Class CaixaEletronico
 */
Class CaixaEletronico {

    /**
     * @var array
     */
    private $notasDisponiveis;

    /**
     * O construtor deve receber um array contendo as notas disponíveis podendo ter quantidade finita
     * ou infinita, para que tenha quantidade finita o array deve seguir o seguinte padrão:
     * [
     *   10 => ["valor" => 10, "quantidade" => 10]
     * ]
     * Caso o array passado não possua o campo "quantidade", o número de notas é infinito para aquela
     * nota
     *
     *
     * CaixaEletronico constructor.
     * @param array $notasDisponiveis
     */
    public function __construct(array $notasDisponiveis)
    {
        krsort($notasDisponiveis);

        $this->notasDisponiveis = $notasDisponiveis;
    }

    /**
     * Método responsável por efetuar o saque do valor e fazer as validações necessárias para o saque
     *
     * @param float $valor
     * @return array
     * @throws Exception
     */
    public function sacar(float $valor): array
    {
        if ($valor < $this->getNotaComValorMaisBaixo()["valor"]) {
            throw new \InvalidArgumentException("O valor mínimo de saque é {$this->getNotaComValorMaisBaixo()["valor"]}");
        }

        return $this->getNotas($valor);
    }

    /**
     * Método responsável por pegar a nota com o valor mais baixo
     * Como o array está ordenado pelo valor descrescente, pega o último item do array
     *
     * @return array|null
     */
    private function getNotaComValorMaisBaixo(): array
    {
        return $this->notasDisponiveis[key(array_slice($this->notasDisponiveis, -1, 1, true))];
    }

    /**
     * Método responsável por retornar as notas que são necessárias para efetuar o saque,
     * Seguindo a regra de entregar o mínimo de notas disponíveis necessárias para efetuar o saque
     * Caso as notas disponíveis não fechem o valor a ser sacado, lança uma Exception
     *
     * @param $valor
     * @return array
     * @throws Exception
     */
    private function getNotas($valor): array
    {
        $notas =  [];
        $i = 0;

        foreach ($this->notasDisponiveis as $nota) {
            if (!empty($nota["quantidade"]) && $nota["quantidade"] == 0) {
                continue;
            }

            $divisaoValorPelaNota = $valor / $nota["valor"];

            if ($divisaoValorPelaNota >= 1) {
                $numeroNotas 			 = floor($divisaoValorPelaNota);

                if (!empty($nota["quantidade"]) && $numeroNotas > $nota["quantidade"]) {
                    $numeroNotas = $nota["quantidade"];
                }

                $valor       			 = $valor - ($nota["valor"] * $numeroNotas);
                $notas[$i]["nota"]       = number_format($nota["valor"], 2, ",", ".");
                $notas[$i]["quantidade"] = (int) $numeroNotas;
                $i++;
            }

            if ($valor == 0) {
                break;
            }
        }

        if ($valor > 0) {
            throw new \Exception("Não é possível sacar o valor solicitado com as notas disponíveis.");
        }

        return $notas;
    }
}
