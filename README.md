# Simple PHP Banking API

This project implements a simple REST API in PHP to simulate banking operations, including deposit, withdrawal and transfer between accounts. Instead of using a database or sessions, the data is stored in a JSON file (`data.json`), allowing persistence of data between requests in a simple way.

## Requirements

- PHP 7.4+
- PHP server with read and write permission to the `data.json` file

## API Endpoints

### Reset API state

Resets the API state, clearing all accounts and their balances.

- **URL:** `/reset`
- **Method:** `POST`
- **Response:** `200 OK`

---

### Get account balance

Gets the balance of a specific account based on the `account_id` provided.

- **URL:** `/balance?account_id={id}`
- **Method:** `GET`
- **Query Parameters:**
  - `account_id` (int): ID of the account to be queried
- **Response:**
  - **Status:** `200 OK`
  - **Body:** Balance of the account (example: `20`)
- **Error Response:**
  - **Status:** `404 Not Found`
  - **Body:** `0`

---

### Execute transactions on an account

Executes deposit, withdrawal or transfer operations on an account.

- **URL:** `/event`
- **Method:** `POST`
- **Body (JSON):** Data of the operation (see below)

#### Event Types

1. **Deposit**
   - **JSON Body:** `{"type": "deposit", "destination": "{id}", "amount": {value}}`
   - **Response:**
     - **Status:** `201 Created`
     - **Body:** `{"destination": {"id": "{id}", "balance": {new_balance}}}`

2. **Withdrawal**
   - **JSON Body:** `{"type": "withdraw", "origin": "{id}", "amount": {value}}`
   - **Response:**
     - **Status:** `201 Created`
     - **Body:** `{"origin": {"id": "{id}", "balance": {new_balance}}}`
   - **Error Response:**
     - **Status:** `404 Not Found`
     - **Body:** `0` (if the account does not exist or the balance is insufficient)

3. **Transfer**
   - **JSON Body:** `{"type": "transfer", "origin": "{id}", "amount": {value}, "destination": "{id}"}`
   - **Response:**
     - **Status:** `201 Created`
     - **Body:** `{"origin": {"id": "{id}", "balance": {new_balance}}, "destination": {"id": "{id}", "balance": {new_balance}}}`
   - **Error Response:**
     - **Status:** `404 Not Found`
     - **Body:** `0` (if the origin account does not exist or the balance is insufficient)

## Example Usage

### 1. Reset the API state
```bash
curl -X POST http://localhost:8080/reset
```

### 2. Create an account with initial balance
```bash
curl -X POST -H "Content-Type: application/json" -d '{"type":"deposit", "destination":"100", "amount":10}' http://localhost:8080/event
```

### 3. Check account balance
```bash
curl -X GET "http://localhost:8080/balance?account_id=100"
```

### 4. Deposit into an existing account
```bash
curl -X POST -H "Content-Type: application/json" -d '{"type":"deposit", "destination":"100", "amount":20}' http://localhost:8080/event
```

### 5. Withdraw from an existing account
```bash
curl -X POST -H "Content-Type: application/json" -d '{"type":"withdraw", "origin":"100", "amount":5}' http://localhost:8080/event
```

### 6. Transfer between accounts
```bash
curl -X POST -H "Content-Type: application/json" -d '{"type":"transfer", "origin":"100", "amount":5, "destination":"200"}' http://localhost:8080/event
```

## Notes

- The `data.json` file is used to store the state of the accounts. Make sure the PHP server has read and write permissions on this file.
- JSON format is used for communication between client and server. Make sure to send `POST` requests with the `Content-Type: application/json` header.


## Installation and Execution

1. Clone this repository.
```bash
  git clone https://github.com/davidnunesantos/simple-api-php.git
  ```

2. Navigate to the API directory.
```bash
  cd simple-api-php
  ```

3. Start the built-in PHP server.
```bash
  php -S localhost:8080 -t public
  ```

4. Test the endpoints using tools like curl or Postman.
