<?php

declare(strict_types=1);

namespace App\Support\Exceptions;

use DomainException;
use Throwable;

class AppException extends DomainException
{
    public function __construct(
        string $message = 'SERVER_ERROR',
        int $code = 500,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
