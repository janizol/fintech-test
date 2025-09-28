<?php

class BankAccountRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?BankAccount {
        $sql = "SELECT id, balance FROM accounts WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new BankAccount((int)$row['id'], (float)$row['balance']);
        }
        return null;
    }

    public function updateBalance(int $id, float $amount): bool {
        $sql = "UPDATE accounts SET balance = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$amount, $id]);
    }
}
