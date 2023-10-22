<?php

declare(strict_types=1);

namespace App\Action;

use App\Support\Exceptions\UnauthorizedException;
use App\Support\Exceptions\ValidationException;
use Cake\Validation\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;

abstract class BaseAction
{
    protected ServerRequestInterface $request;

    protected ResponseInterface $response;

    protected array $args;

    /**
     * @throws HttpException
     * @throws AppException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        return $this->action();
    }

    abstract protected function action(): ResponseInterface;

    /**
     * @return array|object
     */
    protected function getParsedBody()
    {
        return $this->request->getParsedBody();
    }

    /**
     * @throws ValidationException
     * @return array|object
     */
    protected function getValidatedBody(Validator $validator): array {
        $body = $this->getParsedBody();
        return $this->validateData($body, $validator, 'Please check HTTP body input');
    }

    /**
     * @throws ValidationException
     * @return array|object
     */
    protected function getValidatedQuery(Validator $validator): array {
        $query = $this->request->getQueryParams();
        return $this->validateData($query, $validator, 'Please check HTTP query params');
    }

    /**
     * @throws ValidationException
     * @return array|object
     */
    protected function getValidatedArgs(Validator $validator): array {
        $args = $this->args;
        return $this->validateData($args, $validator, 'Please check url arguments');
    }

    /**
     * @throws ValidationException
     * @return array|object
     */
    public function validateData($data, Validator $validator, string $message = 'Please check your input'): array {
        $errors = $validator->validate($data);

        if ($errors) {
            throw new ValidationException($message, $errors);
        }

        return $data;
    }

    /**
     * @throws UnauthorizedException
     * @return array|object
     */
    public function getToken($isAuthenticated = false): ?array {
        $token = $this->request->getAttribute('token', null);
        if ($isAuthenticated && !isset($token)) {
            throw new UnauthorizedException();
        }
        return $token;
    }

    /**
     * Write JSON to the response body.
     *
     * This method prepares the response object to return an HTTP JSON
     * response to the client.
     *
     * @param mixed $data The data
     *
     * @return ResponseInterface The response
     */
    public function json(
        mixed $data = null,
    ): ResponseInterface {
        $response = $this->response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(
            (string)json_encode(
                $data,
                JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            )
        );

        return $response;
    }
}
