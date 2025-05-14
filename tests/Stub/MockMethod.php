<?php

declare(strict_types=1);

namespace HT\GrpcValidation\Tests\Stub;

use Spiral\RoadRunner\GRPC\Method;

final class MockMethod extends Method
{
    public function __construct(
        public readonly string $name = 'testMethod',
        public readonly string $inputType = 'TestInputType',
        public readonly string $outputType = 'TestOutputType',
    ) {}
}
