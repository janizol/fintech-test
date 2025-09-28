<?php

namespace App\Listeners;

class LogTransactionListener {
    public function __invoke($event): void {
        $class = (new \ReflectionClass($event))->getShortName();
        file_put_contents(
            __DIR__ . '/../../storage/logs/transactions.log',
            "[" . date('Y-m-d H:i:s') . "] $class: Account {$event->accountId}, Amount {$event->amount}\n",
            FILE_APPEND
        );
    }
}
