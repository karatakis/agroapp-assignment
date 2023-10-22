<?php

declare(strict_types=1);

namespace App\Support\Exceptions;

use Throwable;

final class ForbiddenException extends AppException
{
    public function __construct(
        string $message = 'FORBIDDEN',
        Throwable $previous = null,
    ) {
        parent::__construct($message, 403, $previous);
    }
}
