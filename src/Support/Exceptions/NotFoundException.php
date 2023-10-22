<?php

declare(strict_types=1);

namespace App\Support\Exceptions;

use Throwable;

final class NotFoundException extends AppException
{
    public function __construct(
        string $message = 'NOT_FOUND',
        Throwable $previous = null,
    ) {
        parent::__construct($message, 404, $previous);
    }
}
