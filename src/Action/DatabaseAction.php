<?php

declare(strict_types=1);

namespace App\Action;

use Cake\Database\Connection;

abstract class DatabaseAction extends BaseAction
{
    protected Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }
}
