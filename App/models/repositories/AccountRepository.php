<?php

namespace App\Repositories;

use PDO;
use App\Models\Account;

class AccountRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Account {
        $stmt = $this->pdo->prepare("SELECT id, balance FROM accounts WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Account((int)$row['id'], (float)$row['balance']) : null;
    }

    public function updateBalance(int $id, float $balance): bool {
        $stmt = $this->pdo->prepare("UPDATE accounts SET balance = ? WHERE id = ?");
        return $stmt->execute([$balance, $id]);
    }
}
