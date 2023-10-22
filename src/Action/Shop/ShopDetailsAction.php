<?php

declare(strict_types=1);

namespace App\Action\Shop;

use App\Action\DatabaseAction;
use App\Support\Exceptions\NotFoundException;
use Cake\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;

class ShopDetailsAction extends DatabaseAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $validator = new Validator();
        $validator
            ->requirePresence('id', '`id` is required')
            ->uuid('id', '`id` field should be uuid');

        $args = $this->getValidatedArgs($validator);

        $shop = $this
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
            ->leftJoin(['c' => 'categories'], 's.category_id = c.id')
            ->where([
                's.id' => $args['id'],
            ])
            ->execute()
            ->fetch('assoc');

        if (!$shop) {
            throw new NotFoundException('Shop not found');
        }

        $shop['offers'] = $this
            ->queryFactory
            ->newSelect('offers')
            ->select([
                'id',
                'name',
                'description',
            ])
            ->where([
                'shop_id = ' => $shop['id'],
            ])
            ->execute()
            ->fetchAll('assoc') ?: [];

        return $this->json($shop);
    }
}
