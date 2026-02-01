<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Newsletter;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class SubscribeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc,dns,spoof',
                'max:255',
            ],
        ];
    }
}
