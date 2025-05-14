<?php

declare(strict_types=1);

namespace Service;

use Spiral\RoadRunner\GRPC;

interface TestInterface extends GRPC\ServiceInterface
{
    // GRPC specific service name.
    public const NAME = "service.Test";

    /**
    * @param GRPC\ContextInterface $ctx
    * @param Message $in
    * @return Message
    *
    * @throws GRPC\Exception\InvokeException
    */
    public function Echo(GRPC\ContextInterface $ctx, Message $in): Message;

    /**
    * @param GRPC\ContextInterface $ctx
    * @param Message $in
    * @return Message
    *
    * @throws GRPC\Exception\InvokeException
    */
    public function Ping(GRPC\ContextInterface $ctx, Message $in): Message;

    /**
    * @param GRPC\ContextInterface $ctx
    * @param Message $in
    * @return Message
    *
    * @throws GRPC\Exception\InvokeException
    */
    public function Throw(GRPC\ContextInterface $ctx, Message $in): Message;
}