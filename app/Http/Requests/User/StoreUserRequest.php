<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;

class StoreUserRequest extends UserFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => $this->usernameRules(),
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => $this->passwordRules(),
            'role' => $this->roleRules(),
        ];
    }
}
