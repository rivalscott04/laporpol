<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

abstract class UserFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function validatePayload(array $payload): array
    {
        /** @var array<string, mixed> $validated */
        $validated = Validator::make($payload, (new static)->rules())->validate();

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    protected function roleRules(): array
    {
        return [
            'required',
            'string',
            new Enum(UserRole::class),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected function passwordRules(bool $required = true): array
    {
        $rules = $required ? ['required'] : ['nullable'];

        return [
            ...$rules,
            'string',
            Password::defaults(),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected function usernameRules(int|string|null $ignoreUserId = null): array
    {
        return [
            'required',
            'string',
            'max:50',
            'regex:/^[A-Za-z0-9._-]+$/',
            Rule::unique('users', 'username')->ignore($ignoreUserId),
        ];
    }
}
