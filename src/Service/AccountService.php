<?php

namespace App\Service;

require_once '../src/Storage/JsonStorage.php';

use App\Storage\JsonStorage;

class AccountService
{
    /**
     * @var JsonStorage
     */
    private JsonStorage $storage;

    public function __construct()
    {
        $this->storage = new JsonStorage();
    }

    /**
     * Resets the account state by saving an empty array.
     *
     * @return void
     */
    public function reset(): void
    {
        $this->storage->save([]);
    }

    /**
     * Retrieves the balance for a given account ID.
     *
     * @param int $accountId The ID of the account.
     * @return int|null The balance of the account, or null if the account does not exist.
     */
    public function getBalance(int $accountId): ?int
    {
        $accounts = $this->storage->load();
        return $accounts[$accountId]['balance'] ?? null;
    }

    /**
     * Handles an event with given data.
     *
     * Returns an array with the appropriate HTTP status and data.
     *
     * @param array $data The event data.
     * @return array{"status": int, "data": mixed}
     */
    public function handleEvent(array $data): array
    {
        $accounts = $this->storage->load();
        $type = $data['type'];
        $amount = $data['amount'];
        $response = ["status" => 404, "data" => 0];

        if ($type === 'deposit') {
            $destination = $data['destination'];
            // Create the account if it does not exist.
            if (!isset($accounts[$destination])) {
                $accounts[$destination] = ['id' => $destination, 'balance' => 0];
            }
            // Increase the balance of the account.
            $accounts[$destination]['balance'] += $amount;
            // Set the response with the new balance.
            $response = ["status" => 201, "data" => ["destination" => ["id" => $destination, "balance" => $accounts[$destination]["balance"]]]];
        } elseif ($type === 'withdraw') {
            $origin = $data['origin'];
            // Check if the account exists and has enough balance.
            if (isset($accounts[$origin]) && $accounts[$origin]['balance'] >= $amount) {
                // Decrease the balance of the account.
                $accounts[$origin]['balance'] -= $amount;
                // Set the response with the new balance.
                $response = ["status" => 201, "data" => ["origin" => ["id" => $origin, "balance" => $accounts[$origin]["balance"]]]];
            }
        } elseif ($type === 'transfer') {
            $origin = $data['origin'];
            $destination = $data['destination'];
            // Check if the origin account exists and has enough balance.
            if (isset($accounts[$origin]) && $accounts[$origin]['balance'] >= $amount) {
                // Decrease the balance of the origin account.
                $accounts[$origin]['balance'] -= $amount;
                // Create the destination account if it does not exist.
                if (!isset($accounts[$destination])) {
                    $accounts[$destination] = ['id' => $destination, 'balance' => 0];
                }
                // Increase the balance of the destination account.
                $accounts[$destination]['balance'] += $amount;
                // Set the response with the new balances of both accounts.
                $response = [
                    "status" => 201,
                    "data" => [
                        "origin" => ["id" => $origin, "balance" => $accounts[$origin]['balance']],
                        "destination" => ["id" => $destination, "balance" => $accounts[$destination]['balance']]
                    ]
                ];
            }
        }

        // Save the new state of the accounts.
        $this->storage->save($accounts);

        return $response;
    }
}
