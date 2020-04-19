<?php

declare(strict_types=1);

namespace Tests\Console\Command;

use Ahc\Cli\Application;
use Ahc\Cli\IO\Interactor;
use Ahc\Cli\Output\Writer;
use Application\Console\Command\SacarCommand;
use Application\Domain\Entity\CaixaEletronico;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\PhpUnit\ProphecyTrait;

final class SacarCommandTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|null */
    private $caixaEletronico;

    /** @var ObjectProphecy|null */
    private $colorText;

    /** @var ObjectProphecy|null */
    private $logger;

    /** @var ObjectProphecy|null */
    private $application;

    /** @var SacarCommand */
    private $sacarCommand;

    /**
     * Preparação da classe de testes
     */
    protected function setUp(): void
    {
        $this->caixaEletronico = $this->prophesize(CaixaEletronico::class);
        $this->logger          = $this->prophesize(Logger::class);
        $this->application     = $this->prophesize(Application::class);
        $this->interactor      = $this->prophesize(Interactor::class);

        $this->interactor->writer()->willReturn(new Writer());
        $this->caixaEletronico->sacar(Argument::type("float"))->willReturn([]);
        $this->application->io()->willReturn($this->interactor->reveal());

        $this->sacarCommand = new SacarCommand(
            $this->caixaEletronico->reveal(),
            $this->logger->reveal()
        );
    }

    public function testExecuteSucesso()
    {
        $json  = false;
        $valor = 10;

        $valorFormatado = number_format($valor, 2, ",", ".");

        $this->application->argv()->willReturn([$json, $valorFormatado]);
        $this->sacarCommand->bind($this->application->reveal());

        $result = $this->sacarCommand->execute($json, $valor);

        $this->assertEquals(1, $result);
    }

    public function testExecuteErroStringComoValor()
    {
        $json  = false;
        $valor = "teste";

        $this->application->argv()->willReturn([$json, $valor]);
        $this->sacarCommand->bind($this->application->reveal());

        $result = $this->sacarCommand->execute($json, $valor);

        $this->assertEquals(0, $result);
    }

    public function testExecuteErroNullComoValor()
    {
        $json  = false;
        $valor = null;

        $this->application->argv()->willReturn([$json, $valor]);
        $this->sacarCommand->bind($this->application->reveal());

        $result = $this->sacarCommand->execute($json, $valor);

        $this->assertEquals(0, $result);
    }
}