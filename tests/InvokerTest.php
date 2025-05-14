<?php

declare(strict_types=1);

namespace HT\GrpcValidation\Tests;

use HT\GrpcValidation\Exceptions\ValidationException;
use HT\GrpcValidation\Invoker;
use HT\GrpcValidation\Tests\Stub\MockMethod;
use HT\GrpcValidation\Tests\Stub\TestService;
use HT\GrpcValidation\Tests\Stub\ThrowFormRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Mockery;
use PHPUnit\Framework\TestCase;
use Service\Message;
use Spiral\RoadRunner\GRPC\ContextInterface;

class InvokerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_invoke_success_without_validation(): void
    {
        // Mock the Application container
        $app = Mockery::mock(Application::class);
        $app->shouldReceive('get')->andReturnNull();

        // Create Invoker instance
        $invoker = new Invoker($app);

        // Mock Service, Method, Context
        $service = new TestService();
        $method = new MockMethod(
            name: 'Echo',
        );

        $ctx = Mockery::mock(ContextInterface::class);

        // Use custom MockInput and MockOutput
        $input = new Message();

        $output = new Message();
        $output->setMsg('pong');

        // Invoke the method
        $actualOutput = $invoker->invoke($service, $method, $ctx, $input);
        $this->assertEquals($actualOutput, $output->serializeToString());
    }

    public function test_invoke_success_with_validation(): void
    {
        // / Mock the Application container
        $app = Mockery::mock(Application::class);
        $app->shouldReceive('get')->andReturnNull();

        // Mock Validator facade to simulate successful validation
        Validator::shouldReceive('make')->once()->andReturn(
            Mockery::mock(\Illuminate\Validation\Validator::class, function ($mock) {
                $mock->shouldReceive('fails')->once()->andReturn(false);
            })
        );

        // Create Invoker instance
        $invoker = new Invoker($app);

        // Mock Service, Method, Context
        $service = new TestService();
        $method = new MockMethod(
            name: 'Ping',
        );

        $ctx = Mockery::mock(ContextInterface::class);

        // Use custom MockInput and MockOutput
        $input = new Message();
        $input->setMsg('pong');

        $output = new Message();
        $output->setMsg('pong');

        // Invoke the method
        $actualOutput = $invoker->invoke($service, $method, $ctx, $input);

        $this->assertEquals($actualOutput, $output->serializeToString());
    }

    public function test_invoke_fails_with_validation(): void
    {
        // / Mock the Application container
        $app = Mockery::mock(Application::class);
        $app->shouldReceive('get')->andReturn(new ThrowFormRequest());

        // Mock Validator facade to simulate successful validation
        Validator::shouldReceive('make')->once()->andReturn(
            Mockery::mock(\Illuminate\Validation\Validator::class, function ($mock) {
                $mock->shouldReceive('fails')->once()->andReturn(true);
                $mock->shouldReceive('errors')->once()->andReturn(
                    Mockery::mock(\Illuminate\Support\MessageBag::class, function ($mock) {
                        $mock->shouldReceive('first')->once()->andReturn('Validation failed');
                    })
                );
            })
        );

        // Create Invoker instance
        $invoker = new Invoker($app);

        // Mock Service, Method, Context
        $service = new TestService();
        $method = new MockMethod(
            name: 'Throw',
        );

        $ctx = Mockery::mock(ContextInterface::class);

        // Use custom MockInput and MockOutput
        $input = new Message();
        $input->setMsg('pong');

        $this->expectException(ValidationException::class);

        // Invoke the method
        $invoker->invoke($service, $method, $ctx, $input);
    }
}
