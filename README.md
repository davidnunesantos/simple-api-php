# API Simples de Gerenciamento de Contas em PHP

Este é uma API simples construída em PHP que oferece as possibilidades básicas de gerenciamento do saldo da conta bancária, como **depósito**, **saque**, **transferência** e **consulta de saldo**. Este trabalho usa as sessões para armazenar dados temporariamente, então todas as informações serão resetadas quando a sessão expirar.

## Funcionalidades

- **Resetar estado da API**
- **Consultar saldo de uma conta**
- **Criar conta e depositar saldo**
- **Sacar de uma conta existente**
- **Transferir saldo entre contas**

## Endpoints

### Resetar o estado da API

Reseta o estado da API, removendo todas as contas e seus saldos.

- **URL**: `/reset`
- **Método**: `POST`
- **Resposta**:
  - `200 OK`

### Consultar Saldo

Retorna o saldo de uma conta existente.

- **URL**: `/balance`
- **Método**: `GET`
- **Parâmetros de Query**:
  - `account_id`: ID da conta que você deseja consultar.
- **Resposta**:
  - `200` com saldo, se a conta existir.
  - `404` com `0`, se a conta não existir.

### Criar Conta e Depositar

Cria uma conta com um saldo inicial ou deposita um valor em uma conta existente.

- **URL**: `/event`
- **Método**: `POST`
- **Body**:
  ```json
  {
    "type": "deposit",
    "destination": "100",
    "amount": 10
  }
  ```
- Resposta
    - `201` com o saldo atualizado da conta de destino.

### Sacar de uma Conta Existente

Realiza um saque em uma conta existente, caso tenha saldo suficiente.

- **URL**: `/event`
- **Método**: `POST`
- **Body**:
  ```json
  {
    "type": "withdraw",
    "origin": "100",
    "amount": 5
  }
  ```
- Resposta
    - `201` com o saldo atualizado da conta de origem.
    - `404` com `0`, se a conta não existir ou o saldo for insuficiente.

### Transferir entre Contas

Transfere um valor de uma conta de origem para uma conta de destino. A conta de destino será criada se não existir.

- **URL**: `/event`
- **Método**: `POST`
- **Body**:
  ```json
  {
    "type": "transfer",
    "origin": "100",
    "amount": 15,
    "destination": "300"
  }
  ```
- Resposta
    - `201` com os saldos atualizados das contas de origem e destino.
    - `404` com `0`, se a conta de origem não existir ou não tiver saldo suficiente.

## Exemplo de Uso

#### Resetar a API

  ```bash
  curl -X POST http://localhost:8080/reset
  ```

#### Criar uma conta com depósito inicial

  ```bash
  curl -X POST http://localhost:8080/event -d '{"type":"deposit", "destination":"100", "amount":10}'
  ```

#### Consultar saldo

  ```bash
  curl -X GET http://localhost:8080/balance?account_id=100
  ```

#### Realizar um saque

  ```bash
  curl -X POST http://localhost:8080/event -d '{"type":"withdraw", "origin":"100", "amount":5}'
  ```

#### Transferir entre contas

  ```bash
  curl -X POST http://localhost:8080/event -d '{"type":"transfer", "origin":"100", "amount":5, "destination":"200"}'
  ```

## Requisitos

- **PHP 7.4 ou superior.**
- **Servidor Web local, como Apache ou Nginx**

## Instalação e Execução

1. Clone este repositório.
```bash
  git clone https://github.com/davidnunesantos/simple-api-php.git
  ```

2. Navegue até o diretório da API.
```bash
  cd simple-api-php
  ```

3. Inicie o servidor PHP embutido.
```bash
  php -S localhost:8080
  ```

4. Teste os endpoints usando ferramentas como curl ou Postman.

## Observações

Esta API é uma implementação simplificada e utiliza sessões para armazenar dados temporários.

