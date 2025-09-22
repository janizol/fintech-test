<?php

class WithdrawalEvent {
    private $amount;
    private $accountId;
    private $status;

    public function __construct($amount, $accountId, $status) {
        $this->amount = $amount;
        $this->accountId = $accountId;
        $this->status = $status;
    }

    public function toJson() {
        return json_encode([
            "amount" => (string)$this->amount,
            "accountId" => $this->accountId,
            "status" => $this->status
        ]);
    }
}
