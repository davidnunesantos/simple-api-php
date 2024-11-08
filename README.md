# Simple PHP Banking API

Este projeto implementa uma API REST básica em PHP para simular operações bancárias, incluindo depósito, saque e transferência entre contas. Em vez de utilizar banco de dados ou sessões, os dados são armazenados em um arquivo JSON (`data.json`), permitindo a persistência de dados entre requisições de forma simples.

## Requisitos

- PHP 7.4+
- Servidor PHP com permissão de leitura e gravação no arquivo `data.json`

## Endpoints da API

### Resetar o estado da API

Reseta o estado da API, limpando todas as contas e seus saldos.

- **URL:** `/reset`
- **Método:** `POST`
- **Resposta de sucesso:** `200 OK`

---

### Consultar saldo de uma conta

Consulta o saldo de uma conta específica com base no `account_id` fornecido.

- **URL:** `/balance?account_id={id_da_conta}`
- **Método:** `GET`
- **Parâmetros da Query:**
  - `account_id` (int): ID da conta a ser consultada
- **Resposta de sucesso:**
  - **Status:** `200 OK`
  - **Body:** Saldo da conta (exemplo: `20`)
- **Resposta de erro:** 
  - **Status:** `404 Not Found`
  - **Body:** `0`

---

### Realizar transações em uma conta

Executa operações de depósito, saque ou transferência de valores entre contas.

- **URL:** `/event`
- **Método:** `POST`
- **Body (JSON):** Dados da operação (ver abaixo)

#### Tipos de Eventos

1. **Depósito**
   - **JSON Body:** `{"type": "deposit", "destination": "{id_da_conta}", "amount": {valor}}`
   - **Resposta de sucesso:**
     - **Status:** `201 Created`
     - **Body:** `{"destination": {"id": "{id_da_conta}", "balance": {novo_saldo}}}`

2. **Saque**
   - **JSON Body:** `{"type": "withdraw", "origin": "{id_da_conta}", "amount": {valor}}`
   - **Resposta de sucesso:**
     - **Status:** `201 Created`
     - **Body:** `{"origin": {"id": "{id_da_conta}", "balance": {novo_saldo}}}`
   - **Resposta de erro:** 
     - **Status:** `404 Not Found`
     - **Body:** `0` (caso a conta não exista ou o saldo seja insuficiente)

3. **Transferência**
   - **JSON Body:** `{"type": "transfer", "origin": "{id_da_conta_origem}", "amount": {valor}, "destination": "{id_da_conta_destino}"}`
   - **Resposta de sucesso:**
     - **Status:** `201 Created`
     - **Body:** `{"origin": {"id": "{id_da_conta_origem}", "balance": {novo_saldo}}, "destination": {"id": "{id_da_conta_destino}", "balance": {novo_saldo}}}`
   - **Resposta de erro:** 
     - **Status:** `404 Not Found`
     - **Body:** `0` (caso a conta de origem não exista ou o saldo seja insuficiente)

## Exemplo de Uso

### 1. Resetar o estado
```bash
curl -X POST http://localhost:8080/reset
```

### 2. Criar uma conta com saldo inicial
```bash
curl -X POST -H "Content-Type: application/json" -d '{"type":"deposit", "destination":"100", "amount":10}' http://localhost:8080/event
```

### 3. Consultar saldo de uma conta
```bash
curl -X GET "http://localhost:8080/balance?account_id=100"
```

### 4. Depositar em uma conta existente
```bash
curl -X POST -H "Content-Type: application/json" -d '{"type":"deposit", "destination":"100", "amount":20}' http://localhost:8080/event
```

### 5. Sacar de uma conta existente
```bash
curl -X POST -H "Content-Type: application/json" -d '{"type":"withdraw", "origin":"100", "amount":5}' http://localhost:8080/event
```

### 6. Transferir entre contas
```bash
curl -X POST -H "Content-Type: application/json" -d '{"type":"transfer", "origin":"100", "amount":5, "destination":"200"}' http://localhost:8080/event
```

## Observações

- O arquivo `data.json` é utilizado para armazenar o estado das contas. Certifique-se de que o servidor PHP tenha permissão de leitura e escrita nesse arquivo.
- O formato JSON é utilizado para a comunicação entre cliente e servidor. Certifique-se de enviar as requisições `POST` com o cabeçalho `Content-Type: application/json`.


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