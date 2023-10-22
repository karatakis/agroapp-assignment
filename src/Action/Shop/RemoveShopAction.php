<?php

declare(strict_types=1);

namespace App\Action\Shop;

use App\Action\DatabaseAction;
use App\Support\Exceptions\ForbiddenException;
use App\Support\Exceptions\NotFoundException;
use Cake\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;

class RemoveShopAction extends DatabaseAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $token = $this->getToken(true);

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

        if ($shop['owner_id'] !== $token['owner_id']) {
            throw new ForbiddenException();
        }

        $this
            ->queryFactory
            ->newDelete('shops')
            ->where([
                'id =' => $args['id'],
                'owner_id =' => $token['owner_id'],
            ])
            ->execute();

        return $this->json($shop);
    }
}
