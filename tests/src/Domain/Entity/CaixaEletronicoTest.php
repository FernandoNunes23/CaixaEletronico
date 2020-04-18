<?php

declare(strict_types=1);

namespace Tests\Domain\Entity;

use Application\Domain\Entity\CaixaEletronico;
use PHPUnit\Framework\TestCase;

final class CaixaEletronicoTest extends TestCase
{
    /** @var array Mock da configuração de notas disponiveis */
    private $notasDisponiveis;

    /**
     * Gera os valor defaults para realização dos testes
     */
    protected function setUp(): void
    {
        $this->notasDisponiveis = [
            100 => ["valor" => 100, "quantidade" => 100 ],
            50  => ["valor" => 50 , "quantidade" => 100 ],
            20  => ["valor" => 20 , "quantidade" => 20  ],
            10  => ["valor" => 10 , "quantidade" => 10  ]
        ];
    }

    /**
     * Testa a criação do objeto CaixaEletrônico
     */
    public function testCriarCaixaEletronicoSuccesso()
    {
        $caixaEletronico = new CaixaEletronico($this->notasDisponiveis);

        $this->assertInstanceOf(CaixaEletronico::class, $caixaEletronico);
    }

    /**
     *
     */
    public function testCriarCaixaEletronicoComArraySemChavesNumericas()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("As chaves do array passado devem ser numericas.");

        $notasDisponiveis = [
            "teste" => ["valor" => 100, "quantidade" => 50]
        ];

        new CaixaEletronico($notasDisponiveis);
    }

    /**
     *
     */
    public function testCriarCaixaEletronicoComArraySemValorDaNota()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("O array com informacao da nota, deve conter uma chave chamada 'valor'");

        $notasDisponiveis = [
            100 => ["quantidade" => 50]
        ];

        new CaixaEletronico($notasDisponiveis);
    }

    /**
     *
     */
    public function testCriarCaixaEletronicoComArrayComValorDaNotaNaoNumerico()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("A chave 'valor' com a informacao da nota, deve ser numerica.");

        $notasDisponiveis = [
            100 => ["valor" => "teste", "quantidade" => 50]
        ];

        new CaixaEletronico($notasDisponiveis);
    }

    /**
     *
     */
    public function testCriarCaixaEletronicoComArrayComValorDaNotaDiferenteDaChave()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("A chave 'valor' deve ter o mesmo valor da chave 'valor' dentro da informacao da nota.");

        $notasDisponiveis = [
            50 => ["valor" => 100, "quantidade" => 50]
        ];

        new CaixaEletronico($notasDisponiveis);
    }

    /**
     *
     */
    public function testCriarCaixaEletronicoComArrayComQuantidadeDaNotaNaoNumerico()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("A chave 'quantidade' com a informacao da nota, deve ser numerica.");

        $notasDisponiveis = [
            100 => ["valor" => 100, "quantidade" => "teste"]
        ];

        new CaixaEletronico($notasDisponiveis);
    }

    /**
     *
     */
    public function testCriarCaixaEletronicoComArrayComQuantidadeDaNotaMenorQueZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("A chave 'quantidade' nao pode ser negativo.");

        $notasDisponiveis = [
            100 => ["valor" => 100, "quantidade" => -10]
        ];

        new CaixaEletronico($notasDisponiveis);
    }

    /**
     *
     */
    public function testCriarCaixaEletronicoComArrayComValorDaNotaMenorQueZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("A chave 'valor' deve ter o valor maior que zero.");

        $notasDisponiveis = [
            -10 => ["valor" => -10, "quantidade" => 10]
        ];

        new CaixaEletronico($notasDisponiveis);
    }

    /**
     * @throws \Exception
     */
    public function testSacar10()
    {
        $caixaEletronico = new CaixaEletronico($this->notasDisponiveis);
        $valor = 10.00;

        $notas = $caixaEletronico->sacar($valor);

        $this->assertIsArray($notas);
        $this->assertEquals(10, $notas[0]["nota"]);
        $this->assertEquals(1, $notas[0]["quantidade"]);
    }

    /**
     * @throws \Exception
     */
    public function testSacar180()
    {
        $caixaEletronico = new CaixaEletronico($this->notasDisponiveis);
        $valor = 180.00;

        $notas = $caixaEletronico->sacar($valor);

        $this->assertIsArray($notas);

        $this->assertEquals(100, $notas[0]["nota"]);
        $this->assertEquals(1, $notas[0]["quantidade"]);

        $this->assertEquals(50, $notas[1]["nota"]);
        $this->assertEquals(1, $notas[1]["quantidade"]);

        $this->assertEquals(20, $notas[2]["nota"]);
        $this->assertEquals(1, $notas[2]["quantidade"]);

        $this->assertEquals(10, $notas[3]["nota"]);
        $this->assertEquals(1, $notas[3]["quantidade"]);
    }

    /**
     * @throws \Exception
     */
    public function testSacarValorMenorQueOMinimoPossivel()
    {
        $caixaEletronico = new CaixaEletronico($this->notasDisponiveis);
        $valor = $caixaEletronico->getNotaComValorMaisBaixo()["valor"] - 1;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("O valor minimo de saque e {$caixaEletronico->getNotaComValorMaisBaixo()["valor"]}");

        $caixaEletronico->sacar($valor);
    }

    /**
     * @throws \Exception
     */
    public function testSacarValorSemValorEmNotasDisponiveisParaCobrirOSaque()
    {
        $caixaEletronico = new CaixaEletronico($this->notasDisponiveis);
        $valor = 16.000;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Nao e possivel sacar o valor solicitado com as notas disponiveis.");

        $caixaEletronico->sacar($valor);
    }
}