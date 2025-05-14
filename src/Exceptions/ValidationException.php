<?php

declare(strict_types=1);

namespace HT\GrpcValidation\Exceptions;

use Spiral\RoadRunner\GRPC\Exception\GRPCException;
use Spiral\RoadRunner\GRPC\StatusCode;

class ValidationException extends GRPCException
{
    /**
     * Can be overridden by child classes.
     *
     * @var int
     *
     * @phpstan-ignore-next-line
     */
    protected const CODE = StatusCode::INVALID_ARGUMENT;
}
