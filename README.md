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
E deve ser criada o diretório de logs dentro do diretório raiz do projeto:
```bash
$ mkdir logs
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

## Executando os testes unitários
Para executar os testes unitários deve ser executado no terminal o seguinte comando:
```bash
$ docker-compose run --rm caixa-eletronico-tests /app/vendor/bin/phpunit /app/tests
```

Obs: Como eu utilizei uma lib de CLI para rodar o projeto, ela tem um padrão de retorno direto para o STDOUT do php no 
momento de escrever o retorno, não consegui resolver o problema de mostrar o retorno quando ocorre o teste do método 
'execute()' da classe SacarCommand, então está retornando o STDOUT no terminal quando são executados os testes.

## Detalhes da resolução

### Análise das regras
Conforme análise realizada notei que dentro do sistema teriam 3 entidades:
- CaixaEletronico: Responsavel pela execução da regra de negócio especificada.
- Nota: Responsável por guardar as informações de cada nota.
- Cliente: Responsável por guardar as informações do cliente.     
Como um dos requisitos era resolver da forma mais simples criei somente a Entidade 
CaixaEletronico pois as outras entidades não possuem propriedades especificas e não iriam
influenciar no momento na resolução do problema.

### Ambiente
Para o ambiente utilizei Docker e Docker Compose pela facilidade de execução
em ambientes UNIX.

### Implementação
#### Configuração  
A configuração da aplicação segue o seguinte padrão:  
 ```php
    return [
        "notas_disponiveis" => [
            100 => ["valor" => 100, "quantidade" => 100 ],
            50  => ["valor" => 50 , "quantidade" => 100 ],
            20  => ["valor" => 20 , "quantidade" => 20  ],
            10  => ["valor" => 10 , "quantidade" => 10  ]
        ]
    ];
```
- Onde a chave do array deve ser igual a chave 'valor';
- Caso a quantidade daquela nota seja infinita a chave "quantidade" não deve ser colocada;

#### Lógica de saque
Foi inserida uma entidade CaixaEletronico que realiza a ação de sacar seguindo a seguinte lógica:
- Ao montar a entidade é validado o array de notas disponíveis;
- Após validação o array é ordenado pela chave do maior para o menor;
- Na ação de sacar é iterado o array;
- Em cada nota primeiramente é verificado se existe quantidade definida para aquela nota
e se a quantidade é maior que zero;
- É dividido o valor solicitado de saque pelo valor da nota
- Caso o resultado da divisão é maior que 1, ou seja, o valor do saque
pode ser dividido pelo menos um vez pelo valor da nota, significa que aquela nota deve ser 
retorna.
- Uma vez entrada na condição é removido qualquer número após o ".";
- É validada a quantidade de notas disponiveis caso aquela nota tenha tenha definida
a chave quantidade e a quantidade seja maior que o número de notas necessários para o saque,
então atribui a quantidade total de notas disponíveis daquele valor;
- Após isso diminui o valor da nota * numero de notas necessárias do valor total do saque;
- Atribui a nota e a quantidade retornada ao array de retorno;
- Valida se o valor atual é 0, se sim significa que já retornou todas as notas nessárias;
- Caso não, itera para a próxima nota.
- Se após toda a iteração, ainda restar valor, significa que as notas disponíveis não cobrem o valor
solicitado ou não tem como sacar aquele valor com as notas disponíveis.