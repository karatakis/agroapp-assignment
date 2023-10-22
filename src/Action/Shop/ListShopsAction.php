<?php

declare(strict_types=1);

namespace App\Action\Shop;

use App\Action\DatabaseAction;
use Cake\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;

class ListShopsAction extends DatabaseAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $validator = new Validator();
        $validator
            ->maxLength('city', 100, '`city` query field should not be longer than 200 characters')
            ->uuid('category', '`category` query field should be uuid')
            ->uuid('owner', '`owner` query field should be uuid')
            ->boolean('my_shops', '`my_shops` query field should be boolean');

        $params = $this->getValidatedQuery($validator);

        $query = $this
            ->queryFactory
            ->newSelect(['s' => 'shops'])
            ->select([
                'id'=> 's.id',
                'name'=> 's.name',
                'description'=> 's.description',
                'open_hours'=> 's.open_hours',
                'city'=> 's.city',
                'address'=> 's.address',
                'owner_id'=> 's.owner_id',
                'owner_name'=> 'o.name',
                'category_id'=> 's.category_id',
                'category_name'=> 'c.name',
            ])
            ->leftJoin(['o' => 'owners'], 's.owner_id = o.id')
            ->leftJoin(['c' => 'categories'], 's.category_id = c.id');


        if (isset($params['city'])) {
            $query = $query->andWhere([
                'city LIKE' => '%' . $params['city'] . '%',
            ]);
        }

        if (isset($params['category'])) {
            $query = $query->andWhere([
                'category_id = ' => $params['category'],
            ]);
        }

        if (isset($params['owner'])) {
            $query = $query->andWhere([
                'owner_id = ' => $params['owner'],
            ]);
        }

        $token = $this->getToken();
        if (isset($params['my_shops']) && isset($token)) {
            $query = $query->andWhere([
                'owner_id = ' => $token['owner_id'],
            ]);
        }

        $shops = $query->execute()->fetchAll('assoc') ?: [];

        return $this->json($shops);
    }
}
