<?php

declare(strict_types=1);

namespace App\Action\Auth;

use App\Action\DatabaseAction;
use App\Support\Exceptions\ForbiddenException;
use Cake\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Uid\Uuid;

class RegisterAction extends DatabaseAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        // https://book.cakephp.org/4/en/core-libraries/validation.html
        $validator = new Validator();
        $validator
            ->requirePresence('email', '`email` is required')
            ->email('email', false, '`email` field should be email type')
            ->maxLength('email', 200, '`email` field should not be longer than 200 characters')
            ->requirePresence('name', '`name` is required')
            ->minLength('name', 8, '`name` field should be 2 or more characters in length')
            ->maxLength('name', 100, '`name` field should not be longer than 100 characters')
            ->requirePresence('password', '`password` is required')
            ->minLength('password', 8, '`password` field should be 8 or more characters in length')
            ->maxLength('password', 100, '`password` field should not be longer than 100 characters');

        $body = $this->getValidatedBody($validator);

        // check if user exists
        $owner = $this
            ->connection
            ->selectQuery(
                [
                    'id',
                ],
                'owners'
            )
            ->where(['email = ' => $body['email']])
            ->execute()
            ->fetch('assoc');

        if ($owner) {
            throw new ForbiddenException('Owner already exists');
        }

        // create new user
        $id = Uuid::v4();
        $name = $body['name'];
        $email = $body['email'];
        $password = password_hash($body['password'], PASSWORD_BCRYPT, [ 'cost' => 10 ]);

        // https://book.cakephp.org/4/en/orm/query-builder.html
        $this
            ->connection
            ->insertQuery(
                'owners',
                [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ]
            )
            ->execute();

        return $this->json([
            'message' => 'OWNER_CREATED'
        ]);
    }
}
