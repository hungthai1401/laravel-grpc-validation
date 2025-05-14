<?php

declare(strict_types=1);

namespace HT\GrpcValidation\Exceptions;

use Spiral\RoadRunner\GRPC\Exception\GrpcException;
use Spiral\RoadRunner\GRPC\StatusCode;

class ValidationException extends GrpcException
{
    /**
     * Can be overridden by child classes.
     *
     * @psalm-var StatusCodeType
     * @var int
     */
    protected const CODE = StatusCode::INVALID_ARGUMENT;
}