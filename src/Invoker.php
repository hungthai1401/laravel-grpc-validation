<?php

declare(strict_types=1);

namespace HT\GrpcValidation;

use BadFunctionCallException;
use Google\Protobuf\Internal\Message;
use HT\GrpcValidation\Exceptions\ValidationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use JsonException;
use ReflectionClass;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Spiral\RoadRunner\GRPC\Exception\InvokeException;
use Spiral\RoadRunner\GRPC\InvokerInterface;
use Spiral\RoadRunner\GRPC\Method;
use Spiral\RoadRunner\GRPC\ServiceInterface;
use Spiral\RoadRunner\GRPC\StatusCode;
use Throwable;

final class Invoker implements InvokerInterface
{
    private const ERROR_METHOD_RETURN =
        'Method %s must return an object that instance of %s, ' .
        'but the result provides type of %s';

    private const ERROR_METHOD_IN_TYPE =
        'Method %s input type must be an instance of %s, ' .
        'but the input is type of %s';

    public function __construct(
        private readonly Application $container,
    ) {}

    public function invoke(
        ServiceInterface $service,
        Method $method,
        ContextInterface $ctx,
        string|Message|null $input,
    ): string {
        /** @var callable $callable */
        $callable = [$service, $method->name];

        $input = $input instanceof Message ? $input : $this->makeInput($method, $input);

        // Validate input using attributes
        $this->validateInput($service, $method, $input);

        /** @var Message $message */
        $message = $callable($ctx, $input);

        \assert($this->assertResultType($method, $message));

        try {
            return $message->serializeToString();
        } catch (Throwable $e) {
            throw InvokeException::create($e->getMessage(), StatusCode::INTERNAL, $e);
        }
    }

    /**
     * Checks that the result from the GRPC service method returns the Message object.
     *
     * @throws BadFunctionCallException
     */
    private function assertResultType(Method $method, mixed $result): bool
    {
        if (! $result instanceof Message) {
            $type = \get_debug_type($result);

            throw new BadFunctionCallException(
                \sprintf(self::ERROR_METHOD_RETURN, $method->name, Message::class, $type),
            );
        }

        return true;
    }

    /**
     * Converts the input from the GRPC service method to the Message object.
     *
     * @throws InvokeException
     */
    private function makeInput(Method $method, ?string $body): Message
    {
        try {
            $class = $method->inputType;
            \assert($this->assertInputType($method, $class));

            /** @psalm-suppress UnsafeInstantiation */
            $in = new $class();

            if ($body !== null) {
                $in->mergeFromString($body);
            }

            return $in;
        } catch (Throwable $e) {
            throw InvokeException::create($e->getMessage(), StatusCode::INTERNAL, $e);
        }
    }

    /**
     * Validates the input using validation attributes.
     *
     * @throws InvokeException
     */
    private function validateInput(ServiceInterface $service, Method $method, Message $input): void
    {
        $reflectionClass = new ReflectionClass($service);
        $reflectionMethod = $reflectionClass->getMethod($method->name);

        // Check for Validate attribute on method
        $attributes = $reflectionMethod->getAttributes(Validation::class);
        if (empty($attributes)) {
            return;
        }

        /** @var \HT\GrpcValidation\Validation $validateAttribute */
        $validateAttribute = $attributes[0]->newInstance();

        if ($validateAttribute->formRequest) {
            $formRequest = $this->container->get($validateAttribute->formRequest);
            /** @phpstan-ignore-next-line */
            $rules = $formRequest->rules();
            /** @phpstan-ignore-next-line */
            $messages = $formRequest->messages();
            /** @phpstan-ignore-next-line */
            $attributes = $formRequest->attributes();
        } else {
            $rules = $validateAttribute->rules;
            $messages = $validateAttribute->messages;
            $attributes = $validateAttribute->attributes;
        }

        $data = $this->serializeToJsonArray($input);

        // Perform validation
        /** @phpstan-ignore-next-line */
        $validator = Validator::make($data, $rules, $messages, $attributes);
        if ($validator->fails()) {
            throw new ValidationException(
                $validator->errors()->first(),
            );
        }
    }

    /**
     * Checks that the input of the GRPC service method contains the
     * Message object.
     *
     * @param  class-string  $class
     *
     * @throws InvalidArgumentException
     */
    private function assertInputType(Method $method, string $class): bool
    {
        if (! \is_subclass_of($class, Message::class)) {
            throw new InvalidArgumentException(
                \sprintf(self::ERROR_METHOD_IN_TYPE, $method->name, Message::class, $class),
            );
        }

        return true;
    }

    /**
     * @return array<mixed>|null
     *
     * @throws GPBDecodeException
     * @throws DivisionByZeroError
     *
     * @phpstan-ignore-next-line
     */
    private function serializeToJsonArray(Message $message): ?array
    {
        try {
            // @phpstan-ignore-next-line
            return json_decode($message->serializeToJsonString(), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }
    }
}
