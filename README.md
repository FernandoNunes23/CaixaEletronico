# CaixaEletronico
Sistema responsável por efetuar a lógica de saque de um caixa eletrônico, retornando dentro das notas disponíveis, a quantidade de notas necessárias para o saque.

## Pré-Requisitos
- Docker 19.03.1
- Docker Compose 1.24.0

## Rodando a Aplicação
Para rodar a aplicação deve ser aberto um terminal e rodado o seguinte comando:
```bash
$ docker-compose run --rm caixa-eletronico php /app/application.php VALOR_A_SER_SACADO
```

### Exemplos
Tenta sacar o valor de R$ 100,00.
```bash
$ docker-compose run --rm caixa-eletronico php /app/application.php 100
```
Tenta sacar o valor de R$ 100,20.
```bash
$ docker-compose run --rm caixa-eletronico php /app/application.php 100.20
```



