<?php

declare(strict_types=1);

namespace App\Test\TestCase\Action;

use App\Test\Traits\AppTestTrait;
use PHPUnit\Framework\TestCase;
use Selective\TestTrait\Traits\DatabaseTestTrait;

abstract class BaseCase extends TestCase
{
    use AppTestTrait;
    use DatabaseTestTrait;

    function createBasicUser() {
        $request = $this->createJsonRequest(
            'POST',
            '/api/v1/auth/register',
            [
                'email'=> 'user1@example.com',
                'password'=> '1234agroapps',
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
}
