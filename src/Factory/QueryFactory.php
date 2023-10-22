<?php

declare(strict_types=1);

namespace App\Factory;

use Cake\Database\Connection;
use Cake\Database\Query\DeleteQuery;
use Cake\Database\Query\InsertQuery;
use Cake\Database\Query\SelectQuery;
use Cake\Database\Query\UpdateQuery;

final class QueryFactory
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array|string $table
     *
     * @return SelectQuery<mixed>
     */
    public function newSelect(array|string $table): SelectQuery
    {
        return $this->connection->selectQuery([], $table);
    }

    public function newUpdate(string $table, array $data = []): UpdateQuery
    {
        return $this->connection->updateQuery($table)->set($data);
    }

    public function newInsert(string $table, array $data): InsertQuery
    {
        return $this->connection->insertQuery($table)
            ->insert(array_keys($data))
            ->into($table)
            ->values($data);
    }

    public function newDelete(string $table): DeleteQuery
    {
        return $this->connection->deleteQuery($table);
    }
}
