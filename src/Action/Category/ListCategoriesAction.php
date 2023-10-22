<?php

declare(strict_types=1);

namespace App\Action\Category;

use App\Action\DatabaseAction;
use Psr\Http\Message\ResponseInterface as Response;

class ListCategoriesAction extends DatabaseAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $categories = $this
            ->queryFactory
            ->newSelect('categories')
            ->select([
                'id',
                'name',
            ])
            ->execute()
            ->fetchAll('assoc') ?: [];

        return $this->json($categories);
    }
}
