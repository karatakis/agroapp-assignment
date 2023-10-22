<?php

declare(strict_types=1);

namespace App\Action\Shop;

use App\Action\DatabaseAction;
use Cake\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Uid\Uuid as UidUuid;

class CreateShopAction extends DatabaseAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $token = $this->getToken(true);

        $validator = new Validator();
        $validator
            ->requirePresence('category_id', '`category_id` is required')
            ->uuid('category_id', '`category_id` field should be uuid')
            ->requirePresence('name', '`name` is required')
            ->maxLength('name', 100, '`name` field should not be longer than 100 characters')
            ->requirePresence('description', '`description` is required')
            ->maxLength('description', 255, '`description` field should not be longer than 255 characters')
            ->requirePresence('open_hours', '`open_hours` is required')
            ->maxLength('open_hours', 255, '`open_hours` field should not be longer than 255 characters')
            ->requirePresence('city', '`city` is required')
            ->maxLength('city', 100, '`city` field should not be longer than 100 characters')
            ->maxLength('address', 255, '`address` field should not be longer than 255 characters');

        $data = $this->getValidatedBody($validator);
        $data['owner_id'] = $token['owner_id'];

        if (!isset($data['address'])) {
            $data['address'] = '';
        }

        $data['id'] = UidUuid::v4();

        $this
            ->connection
            ->insertQuery('shops', $data)
            ->execute();

        $shop = $this
            ->connection
            ->selectQuery(
                [
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
                ],
                ['s' => 'shops']
            )
            ->leftJoin(['o' => 'owners'], 's.owner_id = o.id')
            ->leftJoin(['c' => 'categories'], 's.category_id = c.id')
            ->where([
                's.id' => $data['id'],
            ])
            ->execute()
            ->fetch('assoc');

        return $this->json($shop);
    }
}
