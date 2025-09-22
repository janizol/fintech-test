<?php

class BankAccount {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getBalance($accountId) {
        $sql = "SELECT balance FROM accounts WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$accountId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['balance'] : null;
    }

    public function updateBalance($accountId, $amount) {
        $sql = "UPDATE accounts SET balance = balance - ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$amount, $accountId]);
        return $stmt->rowCount() > 0;
    }
}
