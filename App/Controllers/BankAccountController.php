<?php

namespace App\Controllers;

use App\Services\BankAccountService;
use Exception;

class BankAccountController {
    private BankAccountService $service;

    public function __construct(BankAccountService $service) {
        $this->service = $service;
    }

    public function withdraw(int $accountId, float $amount): void {
        try {
            $this->service->withdraw($accountId, $amount);
            echo json_encode(["status" => "success", "message" => "Withdrawal complete"]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function deposit(int $accountId, float $amount): void {
        try {
            $this->service->deposit($accountId, $amount);
            echo json_encode(["status" => "success", "message" => "Deposit complete"]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function balance(int $accountId): void {
        $balance = $this->service->getBalance($accountId);
        echo json_encode(["accountId" => $accountId, "balance" => $balance]);
    }
}
