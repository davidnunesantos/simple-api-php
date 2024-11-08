<?php

header('Content-Type: application/json');

session_start();

// Check and initialize accounts
if (!isset($_SESSION['accounts'])) {
    $_SESSION['accounts'] = [];
}

// Reset state function
function resetState() {
    $_SESSION['accounts'] = [];
    http_response_code(200);
    echo json_encode(["status" => "OK"]);
}

// Get account balance function
function getBalance($accountId) {
    if (isset($_SESSION['accounts'][$accountId])) {
        http_response_code(200);
        echo json_encode($_SESSION['accounts'][$accountId]);
    } else {
        http_response_code(404);
        echo json_encode(0);
    }
}

// Deposit, withdraw and transfer function
function processEvent($data) {
    if ($data['type'] === 'deposit') {
        $destination = $data['destination'];
        $amount = $data['amount'];
        
        if (!isset($_SESSION['accounts'][$destination])) {
            $_SESSION['accounts'][$destination] = ['id' => $destination, 'balance' => 0];
        }

        $_SESSION['accounts'][$destination]['balance'] += $amount;
        http_response_code(201);
        echo json_encode(["destination" => $_SESSION['accounts'][$destination]]);
    } elseif ($data['type'] === 'withdraw') {
        $origin = $data['origin'];
        $amount = $data['amount'];

        if (!isset($_SESSION['accounts'][$origin]) || $_SESSION['accounts'][$origin]['balance'] < $amount) {
            http_response_code(404);
            echo json_encode(0);
        } else {
            $_SESSION['accounts'][$origin]['balance'] -= $amount;
            http_response_code(201);
            echo json_encode(["origin" => $_SESSION['accounts'][$origin]]);
        }
    } elseif ($data['type'] === 'transfer') {
        $origin = $data['origin'];
        $destination = $data['destination'];
        $amount = $data['amount'];

        if (!isset($_SESSION['accounts'][$origin]) || $_SESSION['accounts'][$origin]['balance'] < $amount) {
            http_response_code(404);
            echo json_encode(0);
        } else {
            $_SESSION['accounts'][$origin]['balance'] -= $amount;

            if (!isset($_SESSION['accounts'][$destination])) {
                $_SESSION['accounts'][$destination] = ['id' => $destination, 'balance' => 0];
            }

            $_SESSION['accounts'][$destination]['balance'] += $amount;

            http_response_code(201);
            echo json_encode([
                "origin" => $_SESSION['accounts'][$origin],
                "destination" => $_SESSION['accounts'][$destination]
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
