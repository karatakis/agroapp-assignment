<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Support\Exceptions\AppException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AppExceptionMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (AppException $exception) {
            $response = $this->responseFactory->createResponse();
            $data = $this->transform($exception);
            $json = (string)json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
            $response->getBody()->write($json);

            return $response
                ->withStatus(422)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    private function transform(AppException $exception): array
    {
        $error = [
            'message' => $exception->getMessage(),
        ];

        return ['error' => $error];
    }
}
