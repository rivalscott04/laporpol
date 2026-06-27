<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;

class UpdateProfileRequest extends UserFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => $this->passwordRules(required: false),
        ];
    }
}
