<?php

declare(strict_types=1);

namespace Application\Domain\Entity;

/**
 * Class CaixaEletronico
 */
final class CaixaEletronico {

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
        $this->validaFormatoNotasDisponiveis($notasDisponiveis);

        krsort($notasDisponiveis);

        $this->notasDisponiveis = $notasDisponiveis;
    }

    /**
     * @param array $notasDisponiveis
     */
    private function validaFormatoNotasDisponiveis(array $notasDisponiveis)
    {
        foreach ($notasDisponiveis as $key => $nota) {
            if (!is_numeric($key)) {
                throw new \InvalidArgumentException("As chaves do array passado devem ser numericas.");
            }

            if (empty($nota["valor"])) {
                throw new \InvalidArgumentException("O array com informacao da nota, deve conter uma chave chamada 'valor'");
            }

            if (!is_numeric($nota["valor"])) {
                throw new \InvalidArgumentException("A chave 'valor' com a informacao da nota, deve ser numerica.");
            }

            if ($key != $nota["valor"]) {
                throw new \InvalidArgumentException("A chave 'valor' deve ter o mesmo valor da chave 'valor' dentro da informacao da nota.");
            }

            if ($nota["valor"] <= 0) {
                throw new \InvalidArgumentException("A chave 'valor' deve ter o valor maior que zero.");
            }

            if (!empty($nota["quantidade"]) && !is_numeric($nota["quantidade"])) {
                throw new \InvalidArgumentException("A chave 'quantidade' com a informacao da nota, deve ser numerica.");
            }

            if (!empty($nota["quantidade"]) && $nota["quantidade"] < 0) {
                throw new \InvalidArgumentException("A chave 'quantidade' nao pode ser negativo.");
            }
        }
    }

    /**
     * Método responsável por efetuar o saque do valor e fazer as validações necessárias para o saque
     *
     * @param float $valor
     * @return array
     * @throws \Exception
     */
    public function sacar(float $valor): array
    {
        if ($valor < $this->getNotaComValorMaisBaixo()["valor"]) {
            throw new \InvalidArgumentException("O valor minimo de saque e {$this->getNotaComValorMaisBaixo()["valor"]}");
        }

        return $this->getNotas($valor);
    }

    /**
     * Método responsável por pegar a nota com o valor mais baixo
     * Como o array está ordenado pelo valor descrescente, pega o último item do array
     *
     * @return array|null
     */
    public function getNotaComValorMaisBaixo(): array
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
     * @throws \Exception
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
                $notas[$i]["nota"]       = (float) $nota["valor"];
                $notas[$i]["quantidade"] = (int) $numeroNotas;
                $i++;
            }

            if ($valor == 0) {
                break;
            }
        }

        if ($valor > 0) {
            throw new \Exception("Nao e possivel sacar o valor solicitado com as notas disponiveis.");
        }

        return $notas;
    }
}
