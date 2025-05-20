<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'bail',
                'required',
                'string',
                'email',
                'unique:users',
            ],
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ];
    }
}
