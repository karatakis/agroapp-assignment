<?php

declare(strict_types=1);

namespace App\Test\TestCase\Action\Category;

use App\Test\TestCase\Action\BaseCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

/**
 * Test.
 *
 * @coversDefaultClass \App\Action\Customer\CustomerCreatorAction
 */
class ListCategoriesActionTest extends BaseCase
{
    public function testOk(): void
    {
        $this->getConnection()->prepare("INSERT INTO `categories` (`id`, `name`) VALUES
            ('22e5f98c-48cb-4e5e-b14f-9a751408e9de',	'Toys'),
            ('67d7a36a-eefb-483a-889b-ef88a7cecf2a',	'Books');
        ")->execute();

        $request = $this->createJsonRequest(
            'GET',
            '/api/v1/categories'
        );

        $response = $this->app->handle($request);

        // Check response
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJsonContentType($response);

        $json = $this->getJsonData($response);

        assertEquals($json, [
            [
                'id'=> '22e5f98c-48cb-4e5e-b14f-9a751408e9de',
                'name'=> 'Toys',
            ],
            [
                'id'=> '67d7a36a-eefb-483a-889b-ef88a7cecf2a',
                'name'=> 'Books',
            ]
        ]);
    }
}