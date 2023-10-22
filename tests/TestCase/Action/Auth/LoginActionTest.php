<?php

declare(strict_types=1);

namespace App\Test\TestCase\Action\Auth;

use App\Test\TestCase\Action\BaseCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

/**
 * Test.
 *
 * @coversDefaultClass \App\Action\Customer\CustomerCreatorAction
 */
class LoginActionTest extends BaseCase
{
    public function testOk(): void
    {
        $this->createBasicUser();

        $request = $this->createJsonRequest(
            'POST',
            '/api/v1/auth/login',
            [
                'email'=> 'user1@example.com',
                'password'=> '1234pkdemo1'
            ]
        );

        $response = $this->app->handle($request);

        // Check response
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJsonContentType($response);

        $json = $this->getJsonData($response);

        assertNotNull($json['token']);
    }

    public function testUserNotExists(): void
    {

        $request = $this->createJsonRequest(
            'POST',
            '/api/v1/auth/login',
            [
                'email'=> 'user1@example.com',
                'password'=> '1234pkdemo1'
            ]
        );

        $response = $this->app->handle($request);

        // Check response
        $this->assertSame(401, $response->getStatusCode());
        $this->assertJsonContentType($response);

        $json = $this->getJsonData($response);

        assertNotNull($json['error']);
        assertEquals($json['error'], ['message' => 'User not found']);
    }

    public function testInvalidPassword(): void
    {
        $this->createBasicUser();

        $request = $this->createJsonRequest(
            'POST',
            '/api/v1/auth/login',
            [
                'email'=> 'user1@example.com',
                'password'=> 'fakeasfas'
            ]
        );

        $response = $this->app->handle($request);

        // Check response
        $this->assertSame(401, $response->getStatusCode());
        $this->assertJsonContentType($response);

        $json = $this->getJsonData($response);

        assertNotNull($json['error']);
        assertEquals($json['error'], ['message' => 'Invalid password']);
    }
}