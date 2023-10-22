<?php

declare(strict_types=1);

use App\Middleware\AppExceptionMiddleware;
use App\Middleware\LazyCorsMiddleware;
use App\Middleware\TokenMiddleware;
use App\Middleware\ValidationMiddleware;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return function (App $app) {
    $app->add(TokenMiddleware::class);
    $app->addBodyParsingMiddleware();
    $app->add(ValidationMiddleware::class);
    $app->add(AppExceptionMiddleware::class);
    $app->addRoutingMiddleware();
    $app->add(BasePathMiddleware::class);
    $app->add(ErrorMiddleware::class);
    // TODO: remove this in production
    $app->add(LazyCorsMiddleware::class);
};
