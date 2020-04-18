# Caixa Eletrônico
Sistema responsável por efetuar a lógica de saque de um caixa eletrônico, retornando dentro das notas disponíveis, a quantidade de notas necessárias para o saque.

## Pré-Requisitos
- Docker 19.03.1
- Docker Compose 1.24.0

## Instalação
Para instalar as dependências necessárias para o funcionamento da aplicação deve ser executado o seguinte comando:
```bash
$ docker-compose run --rm composer
```

## Executando a Aplicação
Para rodar a aplicação deve ser aberto um terminal e executado o seguinte comando:
```bash
$ docker-compose run --rm caixa-eletronico php /app/application.php ACAO [OPÇÕES] VALOR_A_SER_SACADO
```

### Exemplos
Solicita o saque de R$ 100,00 e retorna plain text.
```bash
$ docker-compose run --rm caixa-eletronico php /app/application.php sacar 100
```

Solcita o saque de de R$ 100,00 e retorna json.
```bash
$ docker-compose run --rm caixa-eletronico php /app/application.php sacar --json 100
```



