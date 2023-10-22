<?php

declare(strict_types=1);

namespace App\Action\Auth;

use Ahc\Jwt\JWT;
use App\Action\DatabaseAction;
use App\Factory\QueryFactory;
use App\Support\Exceptions\UnauthorizedException;
use Cake\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;

class LoginAction extends DatabaseAction
{
    private JWT $jwt;

    function __construct(QueryFactory $queryFactory, JWT $jwt) {
        parent::__construct($queryFactory);
        $this->jwt = $jwt;
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $validator = new Validator();
        $validator
            ->requirePresence('email', '`email` is required')
            ->email('email', false, '`email` field should be email type')
            ->maxLength('email', 200, '`email` field should not be longer than 200 characters')
            ->requirePresence('password', '`password` is required')
            ->minLength('password', 8, '`password` field should be 8 or more characters in length')
            ->maxLength('password', 100, '`password` field should not be longer than 100 characters');

        $body = $this->getValidatedBody($validator);

        // check if user exists
        $owner = $this
            ->queryFactory
            ->newSelect('owners')
            ->select([
                'id',
                'name',
                'email',
                'password',
            ])
            ->where(['email = ' => $body['email']])
            ->execute()
            ->fetch('assoc');

        if (!$owner) {
            throw new UnauthorizedException('User not found');
        }

        // verify password
        if (!password_verify($body['password'], $owner['password'])) {

            throw new UnauthorizedException('Invalid password');
        }

        // create jwt
        $token = $this->jwt->encode([
            'owner_id'=> $owner['id'],
            'name' => $owner['name']
        ]);

        return $this->json([
            'token' => $token
        ]);
    }
}
