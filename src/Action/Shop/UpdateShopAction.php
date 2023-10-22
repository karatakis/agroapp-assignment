<?php

declare(strict_types=1);

namespace App\Action\Shop;

use App\Action\DatabaseAction;
use App\Support\Exceptions\ForbiddenException;
use App\Support\Exceptions\NotFoundException;
use Cake\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateShopAction extends DatabaseAction
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

        $validator = new Validator();
        $validator
            ->uuid('category_id', '`category_id` field should be uuid')
            ->maxLength('name', 100, '`name` field should not be longer than 100 characters')
            ->maxLength('description', 255, '`description` field should not be longer than 255 characters')
            ->maxLength('open_hours', 255, '`open_hours` field should not be longer than 255 characters')
            ->maxLength('city', 100, '`city` field should not be longer than 100 characters')
            ->maxLength('address', 255, '`address` field should not be longer than 255 characters');

        $data = $this->getValidatedBody($validator);
        $shop = array_merge($shop, $data);

        $this
            ->queryFactory
            ->newUpdate('shops', [
                'category_id' => $shop['category_id'],
                'name' => $shop['name'],
                'description' => $shop['description'],
                'open_hours' => $shop['open_hours'],
                'city' => $shop['city'],
                'address' => $shop['address'],
            ])
            ->where([
                'id' => $shop['id'],
                'owner_id' => $token['owner_id']
            ])
            ->execute();

        return $this->json($shop);
    }
}
