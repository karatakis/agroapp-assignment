<?php

declare(strict_types=1);

namespace App\Support\Exceptions;

use Throwable;

final class ValidationException extends AppException
{
    private array $errors;

    public function __construct(
        string $message = 'VALIDATION_ERROR',
        array $errors = [],
        Throwable $previous = null
    ) {
        parent::__construct($message, 422, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
