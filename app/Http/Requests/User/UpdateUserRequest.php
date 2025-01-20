<?php

namespace App\Http\Requests\User;

use App\Enums\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
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
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'username' => ['sometimes', 'required', 'string', 'lowercase', 'max:50', Rule::unique('users')->ignore($this->user->id)],
            'password' => ['sometimes', 'required', 'confirmed', Rules\Password::defaults()],
            'role' => ['sometimes', 'required',  Rule::enum(RoleEnum::class)],
        ];
    }
}
