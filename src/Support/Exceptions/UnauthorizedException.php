<?php

declare(strict_types=1);

namespace App\Support\Exceptions;

use Throwable;

final class UnauthorizedException extends AppException
{
    public function __construct(
        string $message = 'UNAUTHORIZED',
        Throwable $previous = null
    ) {
        parent::__construct($message, 401, $previous);
    }
}
