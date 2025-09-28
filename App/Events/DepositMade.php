<?php

namespace App\Events;

class DepositMade {
    public int $accountId;
    public float $amount;

    public function __construct(int $accountId, float $amount) {
        $this->accountId = $accountId;
        $this->amount = $amount;
    }
}
