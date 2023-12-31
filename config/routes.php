<?php

declare(strict_types=1);

// Define app routes

use App\Action\Auth\LoginAction;
use App\Action\Auth\RegisterAction;
use App\Action\Category\ListCategoriesAction;
use App\Action\Offer\CreateOfferAction;
use App\Action\Shop\CreateShopAction;
use App\Action\Shop\ListShopsAction;
use App\Action\Shop\RemoveShopAction;
use App\Action\Shop\ShopDetailsAction;
use App\Action\Shop\UpdateShopAction;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    $app->get('/', \App\Action\Home\HomeAction::class)->setName('home');

    // API
    $app->group(
        '/api/v1',
        function (RouteCollectorProxy $app) {
            $app->group('/auth', function (Group $authGroup) {
                $authGroup->post('/register', RegisterAction::class);
                $authGroup->post('/login', LoginAction::class);
            });

            $app->group('/shops', function (Group $shopGroup) {
                $shopGroup->get('', ListShopsAction::class);
                $shopGroup->post('', CreateShopAction::class);

                $shopGroup->get('/{id}', ShopDetailsAction::class);
                $shopGroup->post('/{id}', UpdateShopAction::class);
                $shopGroup->delete('/{id}', RemoveShopAction::class);
            });

            $app->group('/offers', function (Group $offerGroup) {
                $offerGroup->post('', CreateOfferAction::class);
            });

            $app->group('/categories', function (Group $categoryGroup) {
                $categoryGroup->get('', ListCategoriesAction::class);
            });
        }
    );
};
