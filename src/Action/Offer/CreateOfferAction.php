<?php

declare(strict_types=1);

namespace App\Action\Offer;

use App\Action\DatabaseAction;
use App\Support\Exceptions\ForbiddenException;
use App\Support\Exceptions\NotFoundException;
use Cake\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Uid\Uuid as UidUuid;

class CreateOfferAction extends DatabaseAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $token = $this->getToken(true);

        $validator = new Validator();
        $validator
            ->requirePresence('shop_id', '`shop_id` is required')
            ->uuid('shop_id', '`shop_id` field should be uuid')
            ->requirePresence('name', '`name` is required')
            ->maxLength('name', 100, '`name` field should not be longer than 100 characters')
            ->requirePresence('description', '`description` is required')
            ->maxLength('description', 255, '`description` field should not be longer than 255 characters');

        $data = $this->getValidatedBody($validator);

        // check if shop exists and if it is owned by owner
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
                's.id' => $data['shop_id'],
            ])
            ->execute()
            ->fetch('assoc');

        if (!$shop) {
            throw new NotFoundException('Shop not found');
        }

        if ($shop['owner_id'] !== $token['owner_id']) {
            throw new ForbiddenException();
        }

        // create offer
        $data['id'] = UidUuid::v4();
        $this
            ->connection
            ->insertQuery('offers', $data)
            ->execute();

        // create emails
        $topic = 'New Offer - '. $shop['name'] . ': ' . $data['name'];
        $body = $data['description'];

        $sql = "INSERT INTO emails_queue (email, topic, body)
            SELECT email, :topic AS topic, :body AS body
            FROM owners
        ";

        $this->connection->execute($sql, [
            'topic' => $topic,
            'body' => $body,
        ]);

        return $this->json($data);
    }
}
