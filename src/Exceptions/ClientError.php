<?php

namespace D4rk0s\Mangopay\Exceptions;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

class ClientError extends \Exception
{
    public function __construct(string $message = "", int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}