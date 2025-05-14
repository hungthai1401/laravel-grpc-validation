# Laravel gRPC Validation

A Laravel package for gRPC validation inspired by [friendsofhyperf/grpc-validation](https://github.com/friendsofhyperf/grpc-validation). This package provides a seamless way to validate gRPC requests in Laravel applications using Attributes.

## Features

- **Attribute-based Validation**: Utilize PHP Attributes to define validation rules directly on your gRPC service methods.
- **Flexible Rule Definition**: Supports passing validation rules as an array or using a Laravel `FormRequest` class for more complex validation logic.
- **PHP Compatibility**: Designed to work with PHP 8.0 and above, ensuring modern PHP features and compatibility.
- **Strict Standards**: Adheres to PSR standards and passes PHPStan at the strictest level for robust code quality.
- **Unit Tested**: Comprehensive unit tests are included to ensure reliability and maintainability.
- **Final Classes and Strict Types**: Uses `final` classes and strict type declarations for better code integrity and predictability.

## Installation

You can install the package via Composer:

```bash
composer require hungthai1401/laravel-grpc-validation
```

## Usage

First of all, you need to set up your gRPC server.

```php
$worker = Worker::create();
$invoker = $this->container->make(Invoker::class);
$server = new GrpcServer($invoker);

$server->registerService(..., ...);

// The server will use the configuration from .rr.yaml or environment
$server->serve($worker);

return 0;
```

Then you need to define your gRPC service methods with validation attributes. Here's an example:

```php
use HT\GrpcValidation\Validation;
use GoodByeFormRequest;

#[Validation(
    rules: [
        'name' => 'required|string|max:10',
        'message' => 'required|string|max:500',
    ],
    messages: [
        'name.required' => 'Name is required',
        'name.string' => 'Name must be a string',
        'name.max' => 'Name must not exceed 10 characters',
        'message.required' => 'Message is required',
        'message.string' => 'Message must be a string',
        'message.max' => 'Message must not exceed 500 characters',
    ],
    attributes: [
        'name' => 'Name',
        'message' => 'Message',
    ],
)]
public function sayHello(HiUser $user) 
{
    $message = new HiReply();
    $message->setMessage("Hello World");
    $message->setUser($user);
    return $message;
}

#[Validation(formRequest: GoodByeFormRequest::class)]
public function sayGoodBye(GoodByeUser $user) 
{
    $message = new GoodByeReply();
    $message->setMessage("Goodbye World");
    $message->setUser($user);
    return $message;
}
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request or open an Issue on GitHub.

## Thanks
- [Hyperf grpc-validation](https://github.com/friendsofhyperf/grpc-validation)
- [Spiral Framework](https://spiral.dev/)

## License
This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.