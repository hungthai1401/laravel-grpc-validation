<?php

namespace HT\GrpcValidation\Tests;

use HT\GrpcValidation\Exceptions\ValidationException;
use HT\GrpcValidation\Invoker;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Mockery;
use PHPUnit\Framework\TestCase;
use Spiral\RoadRunner\GRPC\ContextInterface;
use HT\GrpcValidation\Tests\Stub\MockMethod;
use HT\GrpcValidation\Tests\Stub\TestService;
use HT\GrpcValidation\Tests\Stub\ThrowFormRequest;
use Service\Message;

class InvokerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testInvokeSuccessWithoutValidation(): void
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

    public function testInvokeSuccessWithValidation(): void
    {
        /// Mock the Application container
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

    public function testInvokeFailsWithValidation(): void
    {
        /// Mock the Application container
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
