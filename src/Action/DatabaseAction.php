<?php

declare(strict_types=1);

namespace App\Action;

use App\Factory\QueryFactory;

abstract class DatabaseAction extends BaseAction
{
    protected QueryFactory $queryFactory;

    public function __construct(QueryFactory $queryFactory) {
        $this->queryFactory = $queryFactory;
    }
}
