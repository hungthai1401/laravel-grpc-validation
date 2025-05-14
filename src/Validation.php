<?php

declare(strict_types=1);

namespace HT\GrpcValidation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Validation
{
    /**
     * Create a new validation attribute instance.
     *
     * @param  array<string, mixed>  $rules
     * @param  array<string, string>  $messages
     * @param  array<string, string>  $attributes
     */
    public function __construct(
        public readonly array $rules = [],
        public readonly array $messages = [],
        public readonly array $attributes = [],
        public readonly string $formRequest = '',
    ) {}
}
