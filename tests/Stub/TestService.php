<?php

declare(strict_types=1);

namespace HT\GrpcValidation\Tests\Stub;

use HT\GrpcValidation\Validation;
use Service\Message;
use Service\TestInterface;
use Spiral\RoadRunner\GRPC\ContextInterface;

class TestService implements TestInterface
{
    public function Echo(ContextInterface $ctx, Message $in): Message
    {
        return $in->setMsg('pong');
    }

    #[Validation(
        rules: [
            'msg' => [
                'required',
                'string',
                'min:3',
                'max:10',
            ],
        ],
    )]
    public function Ping(ContextInterface $ctx, Message $in): Message
    {
        return $in->setMsg('pong');
    }

    #[Validation(
        rules: [
            'msg' => [
                'required',
                'string',
                'min:3',
                'max:10',
            ],
        ],
    )]
    public function Throw(ContextInterface $ctx, Message $in): Message
    {
        return $in->setMsg('pong');
    }
}
