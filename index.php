<?php

header('Content-Type: application/json');

define('DATA_FILE', 'data.json');

/**
 * Load account data from JSON file.
 *
 * @return array<int, array{balance: int, id: int}> The account data or an empty array if the file does not exist or data is invalid.
 */
function loadData(): array {
    if (file_exists(DATA_FILE)) {
        $data = file_get_contents(DATA_FILE);
        return json_decode($data, true) ?: [];
    }
    return [];
}

/**
 * Save account data to JSON file.
 *
 * @param array<int, array{balance: int, id: int}> $data The data to be saved. The file is overwritten.
 *
 * @return void
 */
function saveData(array $data): void {
    file_put_contents(DATA_FILE, json_encode($data));
}

/**
 * Reset the account data to an empty array.
 *
 * @return void
 */
function resetState(): void {
    saveData([]);
    http_response_code(200);
    echo "OK";
}

/**
 * Get the balance of a specific account.
 *
 * This function retrieves the balance for a given account ID from the stored account data.
 *
 * @param int $accountId The ID of the account to retrieve the balance for.
 *
 * @return void
 */
function getBalance(int $accountId): void {
    $accounts = loadData();

    if (isset($accounts[$accountId])) {
        http_response_code(200);
        echo json_encode($accounts[$accountId]['balance']);
    } else {
        http_response_code(404);
        echo json_encode(0);
    }
}

/**
 * Deposit, withdraw and transfer function.
 *
 * @param array{type: string, amount: int, origin?: int, destination?: int} $data The event data.
 *
 * @return void Outputs the new balance as a JSON response. If the account does not exist, outputs 0 and sets a 404 response code.
 */
function processEvent(array $data): void {
    $accounts = loadData();

    if ($data['type'] === 'deposit') {
        $destination = $data['destination'];
        $amount = $data['amount'];
        
        if (!isset($accounts[$destination])) {
            $accounts[$destination] = ['id' => $destination, 'balance' => 0];
        }

        $accounts[$destination]['balance'] += $amount;
        saveData($accounts);
        
        http_response_code(201);
        echo json_encode(["destination" => $accounts[$destination]]);
    } elseif ($data['type'] === 'withdraw') {
        $origin = $data['origin'];
        $amount = $data['amount'];

        if (!isset($accounts[$origin]) || $accounts[$origin]['balance'] < $amount) {
            http_response_code(404);
            echo json_encode(0);
        } else {
            $accounts[$origin]['balance'] -= $amount;
            saveData($accounts);
            
            http_response_code(201);
            echo json_encode(["origin" => $accounts[$origin]]);
        }
    } elseif ($data['type'] === 'transfer') {
        $origin = $data['origin'];
        $destination = $data['destination'];
        $amount = $data['amount'];

        if (!isset($accounts[$origin]) || $accounts[$origin]['balance'] < $amount) {
            http_response_code(404);
            echo json_encode(0);
        } else {
            $accounts[$origin]['balance'] -= $amount;

            if (!isset($accounts[$destination])) {
                $accounts[$destination] = ['id' => $destination, 'balance' => 0];
            }

            $accounts[$destination]['balance'] += $amount;
            saveData($accounts);
            
            http_response_code(201);
            echo json_encode([
                "origin" => $accounts[$origin],
                "destination" => $accounts[$destination]
            ]);
        }
    }
}

// Basic router
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method === 'POST' && $path === '/reset') {
    resetState();
} elseif ($method === 'GET' && $path === '/balance') {
    $accountId = $_GET['account_id'];
    getBalance($accountId);
} elseif ($method === 'POST' && $path === '/event') {
    $data = json_decode(file_get_contents('php://input'), true);
    processEvent($data);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
}