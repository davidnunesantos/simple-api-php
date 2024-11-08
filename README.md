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
