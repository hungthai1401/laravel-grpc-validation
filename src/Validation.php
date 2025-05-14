<?php

namespace HT\GrpcValidation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Validation
{
    /**
     * Create a new validation attribute instance.
     *
     * @param array|string $rules
     */
    public function __construct(
        public array $rules = [],
        public array $messages = [],
        public string $formRequest = '',
    ) {
    }
}