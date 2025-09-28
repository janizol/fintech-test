<?php

namespace App\Models;

class Account {
    public int $id;
    public float $balance;

    public function __construct(int $id, float $balance) {
        $this->id = $id;
        $this->balance = $balance;
    }
}
