<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Enums\UserRole;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateUserRequest extends UserFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('record') ?? $this->input('user_id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => $this->usernameRules($userId),
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => $this->passwordRules(required: false),
            'role' => ['sometimes', 'required', 'string', new Enum(UserRole::class)],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }
}
