<?php

declare(strict_types=1);

namespace App\Test\TestCase\Action\Auth;

use App\Test\Traits\AppTestTrait;
use PHPUnit\Framework\TestCase;
use Selective\TestTrait\Traits\DatabaseTestTrait;

/**
 * Test.
 *
 * @coversDefaultClass \App\Action\Customer\CustomerCreatorAction
 */
class RegisterActionTest extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    public function testRegisterOk(): void
    {

        $request = $this->createJsonRequest(
            'POST',
            '/api/v1/auth/register',
            [
                'email'=> 'user1@example.com',
                'password'=> '1234pkdemo1',
                'name'=> 'User One',
            ]
        );

        $response = $this->app->handle($request);

        // Check response
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJsonContentType($response);
        $this->assertJsonData(['message' => 'OWNER_CREATED'], $response);

        // Check logger
        // No logger errors
        $this->assertSame([], $this->getLoggerErrors());

        // Check database
        $this->assertTableRowCount(1, 'owners');
    }

    public function testRegisterNotOk(): void
    {

        $request = $this->createJsonRequest(
            'POST',
            '/api/v1/auth/register',
            [
                'email'=> 'user1example.com',
                'password'=> '1234pkdemo1',
                'name'=> 'User One',
            ]
        );

        $response = $this->app->handle($request);

        // Check response
        $this->assertSame(422, $response->getStatusCode());
        $this->assertJsonContentType($response);
        $this->assertJsonData(['error' => ['message' => 'Please check HTTP body input', 'details' => [ [ 'message' => '`email` field should be email type', 'field' => 'email' ]]]], $response);

        // Check logger
        // No logger errors
        $this->assertSame([], $this->getLoggerErrors());

        // Check database
        $this->assertTableRowCount(0, 'owners');
    }

    public function testRegisterDuplicate(): void
    {
        $this->testRegisterOk();

        $request = $this->createJsonRequest(
            'POST',
            '/api/v1/auth/register',
            [
                'email'=> 'user1@example.com',
                'password'=> '1234pkdemo1',
                'name'=> 'User One',
            ]
        );

        $response = $this->app->handle($request);

        // Check response
        $this->assertSame(403, $response->getStatusCode());
        $this->assertJsonContentType($response);
        $this->assertJsonData(['error' => ['message' => 'Owner already exists']], $response);

        // Check logger
        // No logger errors
        $this->assertSame([], $this->getLoggerErrors());

        // Check database
        $this->assertTableRowCount(1, 'owners');
    }
}