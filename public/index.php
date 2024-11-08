<?php

require_once '../src/Controller/AccountController.php';

use App\Controller\AccountController;

$controller = new AccountController();

/**
 * Handles incoming requests and dispatches them to the appropriate controller methods.
 *
 * @return void
 */
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method === 'POST' && $path === '/reset') {
    // Reset the account state.
    $controller->resetState();
} elseif ($method === 'GET' && $path === '/balance') {
    // Retrieve the balance for the given account ID.
    $accountId = $_GET['account_id'] ?? null;
    $controller->getBalance($accountId);
} elseif ($method === 'POST' && $path === '/event') {
    // Process a new event.
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->processEvent($data);
} else {
    // Return a 404 error for unknown requests.
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
}

