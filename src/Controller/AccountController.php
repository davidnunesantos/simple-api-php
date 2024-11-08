<?php

namespace App\Controller;

require_once '../src/Service/AccountService.php';

use App\Service\AccountService;

class AccountController
{
    private AccountService $service;

    /**
     * Constructor to initialize AccountService.
     */
    public function __construct()
    {
        $this->service = new AccountService();
    }

    /**
     * Resets the account state.
     * Responds with HTTP status 200 and "OK" on success.
     */
    public function resetState(): void
    {
        $this->service->reset();
        http_response_code(200);
        echo "OK";
    }

    /**
     * Retrieves the balance for a given account ID.
     * Responds with HTTP status 200 and balance if found, 
     * 404 and 0 if not, or 400 if account ID is missing.
     *
     * @param int|null $accountId The ID of the account.
     */
    public function getBalance(?int $accountId): void
    {
        if ($accountId === null) {
            http_response_code(400);
            echo json_encode(["error" => "Missing account ID"]);
            return;
        }

        $balance = $this->service->getBalance($accountId);
        
        if ($balance !== null) {
            http_response_code(200);
            echo json_encode($balance);
        } else {
            http_response_code(404);
            echo json_encode(0);
        }
    }

    /**
     * Processes an event with given data.
     * Responds with the appropriate HTTP status and data.
     *
     * @param array $data The event data.
     */
    public function processEvent(array $data): void
    {
        $response = $this->service->handleEvent($data);
        http_response_code($response['status']);
        echo json_encode($response['data']);
    }
}
