# Simple Payment

Sistema de transferência (pagamentos) entre usuários (comuns e lojistas) implementado em [Symfony](https://symfony.com/) com a aplicação das seguintes técnicas/conceitos:

- Domain Driven Desing (DDD);
- Design Patterns;
- S.O.L.I.D;
- Test-Drive Development (TDD);

## Executando a aplicação

Clone o repositório em uma pasta de sua preferência e via terminal execute o seguinte comando para que os containers (PHP, MySQL e Nginx) da aplicação sejam criados e inicializados:

```sh
make up
```

> **Importante:** É necessário possuir o [Docker](https://docs.docker.com/get-docker/) instalado para executar a aplicação.

O processo de inicialização do container PHP instala as dependências do projeto e executa as migrations, logo, é necessário aguardar o término desse processo para utilizar a aplicação. 

Para isso, é possível monitorar os logs dos containers através do comando: 

```sh
make logs
```

A aplicação está pronta para uso quando a seguinte mensagem for exibida:

```
simple_payment_php | 2021-02-25T02:18:36.738500674Z - Running Migrations
simple_payment_php | 2021-02-25T02:18:37.035665052Z [notice] Migrating up to DoctrineMigrations\Version20210223235948
simple_payment_php | 2021-02-25T02:18:37.095691434Z [notice] finished in 53.1ms, used 18M memory, 1 migrations executed, 1 sql queries
simple_payment_php | 2021-02-25T02:18:37.095728450Z 
simple_payment_php | 2021-02-25T02:18:37.103607191Z - Done
simple_payment_php | 2021-02-25T02:18:37.136811488Z [25-Feb-2021 02:18:37] NOTICE: fpm is running, pid 1
simple_payment_php | 2021-02-25T02:18:37.137497410Z [25-Feb-2021 02:18:37] NOTICE: ready to handle connections
```

> A mensagem apresentada no log acima indica que as migrations já foram executadas e o PHP-FPM está pronto para ser consumido.

## Rotas

A partir da inicialização da aplicação é possível consumí-la através de um cliente HTTP como o [Postman](https://www.postman.com/) ou [Insomia](https://insomnia.rest/). 
> **Importante:** A aplicação estará disponível em ***localhost:8080.***

### Criando usuários

Através dessa rota é possível criar usuários comuns e lojistas.

#### Requisição

**HTTP Method:** POST

**Rota:** /user 

**Corpo da Requisição:**

```json
{
    "full_name": "Elias Benjamin Costa",
    "cpf_cnpj": "510.987.971-08",
    "email": "eeliasbenjamincosta@sinalmanaus.com.br",
    "password": "WZ3XXwJShU",
    "amount": 1000,
    "type": "common"
}
```

> **Importante:** O parâmetro `type` representa o tipo de usuário que será criado:
>
> - **common**: Cria um usuário comum que pode receber e realizar transações;
> - **shopkeeper**: Cria um usuário do  tipo lojista que pode somente receber transações;

------

#### Resposta

```json
// HTTP 201 Created
{
    "id": "f48d4f54-3960-4847-b2d0-e8ee1be77fcc"
}
```

O id do usuário criado é retornado como resposta da requisição.

> **Dica:** Leia [Por que utilizar UUID.](##esclarecimentos)

### Consultando Usuários

Através dessa rota é possível consultar os dados de um usuário.

#### Requisição

**HTTP Method:** GET

**Rota:** /user/{id}

**Exemplo:** /user/f48d4f54-3960-4847-b2d0-e8ee1be77fcc

------

#### Resposta

```json
// HTTP 200 OK
{
    "id": "f48d4f54-3960-4847-b2d0-e8ee1be77fcc",
    "full_name": "Elias Benjamin Costa",
    "email": "eeliasbenjamincosta@sinalmanaus.com.br",
    "cnpj_cpf": "510.987.971-08",
    "wallet_amount": 1000,
    "type": "common"
}
```

> **Importante:** Assim como na rota de criação de usuário o atributo `type` representa o tipo de usuário. 

### Realizando Transações

Através dessa rota é possível realizar transações financeiras entre os usuários.

**HTTP Method:** POST

**Rota:** /transaction 

**Corpo da Requisição:**

```json
{
    "value" : 100.85,
    "payer" : "cf17a664-b81b-4375-b90c-55c151b8603e",
    "payee" : "3cf61cbc-0400-4ee0-b621-ef0bc07eddba"
}
```

------

#### Resposta

```json
// HTTP 204 No Content
```

Como resposta a rota retorna o código HTTP 204.

## Executando Processos Assíncronos

Quando realizamos uma transação é necessário enviar uma notificação de pagamento, tal processo é realizado em segundo plano por intermédio do [Symfony Messenger](https://symfony.com/doc/current/messenger.html).  

Através desse componente é possível garantir a retentativa de um comando (processo) em caso de erros, além de armazenar ocorrências de comandos que falharam em suas retêntivas. 

Nesse cenário, para executar os processos assíncronos pendentes é necessário executar o seguinte comando: 

```sh
make messenger
```

O comando exibirá logs de processamento conforme o exemplo abaixo:

```
 [OK] Consuming messages from transports "async".                                                                       
                                                                                                                        
 // The worker will automatically exit once it has received a stop signal via the messenger:stop-workers command.       

 // Quit the worker with CONTROL-C.                                                                                     

[25-Feb-2021 03:35:59 UTC] [info] Received message SimplePayment\Core\Application\Async\SendPaymentNotification

[25-Feb-2021 03:36:00 UTC] [info] Message SimplePayment\Core\Application\Async\SendPaymentNotification handled by SimplePayment\Core\Application\Async\SendPaymentNotificationHandler::__invoke

[25-Feb-2021 03:36:00 UTC] [info] SimplePayment\Core\Application\Async\SendPaymentNotification was handled successfully (acknowledging to transport).
```

## Testes Automatizados e Análisadores Estáticos de Código

Para executar os testes automatizados do projeto basta executar o comando: 

```sh
make test
```

Esse comando é responsável por executar os análisadores estáticos de código configurados ([PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer), [PHPStan](https://phpstan.org/user-guide/getting-started) e [Psalm](https://psalm.dev/)) e em seguida executar os testes automatizados implementados. O comando exibirá uma saída semelhante a abaixo: 

```
Executando testes
> phpcs
> phpstan analyse --memory-limit=-1
Note: Using configuration file /app/phpstan.neon.
 89/89 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%
                                                                                                                        
 [OK] No errors                                                                                                         
                                                                                                                        
> psalm
Scanning files...
Analyzing files...

░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ 60 / 88 (68%)
░░░░░░░░░░░░░░░░░░░░░░░░░░░░

------------------------------
No errors found!
------------------------------
2 other issues found.
You can display them with --show-info=true
------------------------------

Checks took 5.59 seconds and used 278.572MB of memory
Psalm was able to infer types for 98.8986% of the codebase
> phpunit
PHPUnit 9.5.2 by Sebastian Bergmann and contributors.

Warning:       Your XML configuration validates against a deprecated schema.
Suggestion:    Migrate your XML configuration using "--migrate-configuration"!

...............................................................  63 / 111 ( 56%)
................................................                111 / 111 (100%)

Time: 00:01.715, Memory: 42.50 MB

OK (111 tests, 247 assertions)
```

> **Importante:** Os analisadores estáticos de código são executados na seguinte ordem:
>
> - PHP_CodeSniffer (phpcs);
> - PHPStan (phpstan);
> - Psalm (psalm);

### Coverage

Para gerar e exibir o relatório de coverage (cobertura) alcançado pelos testes automatizados implementados basta executar o seguinte comando:

```sh
make coverage
```

O comando exibirá uma saída com o sumário da porcentagem de cobertura de código, além de uma análise de cobertura por classes, conforme pode-se observar abaixo:

```
Code Coverage Report:      
  2021-02-25 03:52:19      
                           
 Summary:                  
  Classes: 92.86% (39/42)  
  Methods: 96.61% (114/118)
  Lines:   94.06% (364/387)

SimplePayment\Core\Application\Async\SendPaymentNotification
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% (  3/  3)
```

## Melhorias Sugeridas

### Event Sourcing

O `DomainEventPublisher` é responsável por receber os eventos de domínio (`DomainEvent`) e enviá-los para os seus `DomainEventSubscriber` cadastrados.

O próximo passo é o armazenamento dos eventos de domínio com o intuito de alcançar o [Event sourcing](https://microservices.io/patterns/data/event-sourcing.html).

### Monitoramento no Error Handler

A aplicação possui um Exception Listener que recebe as exceções lançadas, dentro do listener existe uma [Chain Of Responsability](https://refactoring.guru/pt-br/design-patterns/chain-of-responsibility) de Error Handler, em que cada Error Handler é responsável por formatar um tipo específico de exceção em uma resposta JSON.

Afim de melhorar o gerenciamento da aplicação, seria possível adicionar uma ferramenta de monitoramento como (NewRelic ou Sentry) aos nós da corrente (Error Handler), com intuito de que as exceções do sistema sejam monitoradas.

## Esclarecimentos

### Por que utilizar o Symfony?

De maneira sucinta o Symfony apresenta algums benefícios em relação à estrutura do projeto e a sua manutenção à longo prazo, o que o torna mais adequado para projetos maiores e complexos. 

Uma vez que é dividido em módulos integráveis é possível importar somente as funcionalidades que o sistema realmente utilizará, o tornando mais conciso. Além disso, possui mecanismos mais claros e transparentes que facilitam a aplicação de padrões e boas práticas como: Injeção de Depência, Test-Drive Development (TDD), entre outros.

### Por que utilizar que UUID?

Dentre os benefícios recebidos devido ao do uso do [UUID](https://pt.wikipedia.org/wiki/Identificador_%C3%BAnico_universal), podemos citar a integridade das entidades do sistema. 

Quando utilizarmos uma estratégia de geração automatica de ID as entidades do sistema (Domain Object) permanecem inválidas até que sejam persistidas no banco de dados, uma vez que ainda não possuem ID. Nesse cenário, torna-se mais complexo testar as entidades, uma vez que em alguns cenários o ID da entidade será necessário.

Logo, para resolver essa questão seria necessário persistir as entidades no banco de dados, porém, isso torna o teste mais lento uma vez que dependeremos do I/O do banco de dados.

Com o UUID tais problemas não acontecem, pois, uma vez que o UUID sempre será único é possível adicioná-lo ao construtor da entidade para que seus objetos sempre tenham um estado válido.