<?php

declare(strict_types=1);

namespace HT\GrpcValidation\Tests\Stub;

use Illuminate\Foundation\Http\FormRequest;

final class ThrowFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'msg' => 'required|string|min:5|max:10',
        ];
    }
}
