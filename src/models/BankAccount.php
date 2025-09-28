<?php

class BankAccount {
    public int $id;
    public float $balance;

    public function __construct(int $id, float $balance) {
        $this->id = $id;
        $this->balance = $balance;
    }
}
