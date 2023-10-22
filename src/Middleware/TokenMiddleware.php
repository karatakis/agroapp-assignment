<?php

declare(strict_types=1);

namespace App\Middleware;

use Ahc\Jwt\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class TokenMiddleware implements MiddlewareInterface
{
    private JWT $jwt;

    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $header = $request->getHeader('Authorization');

        if (count($header) == 1 && str_starts_with($header[0], 'Bearer ')) {
            $parts = explode(' ', $header[0]);
            if (count($parts) == 2) {
                $parsed = $this->jwt->decode($parts[1]);
                $request = $request->withAttribute('token', $parsed);
            }
        }

        return $handler->handle($request);
    }
}
