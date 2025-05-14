<?php

declare(strict_types=1);

namespace HT\GrpcValidation;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Spiral\RoadRunner\GRPC\InvokerInterface;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register any necessary bindings or singletons here
        $this->app->bind(InvokerInterface::class, fn ($app) => new Invoker($app));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}
}
