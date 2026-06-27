<?php

declare(strict_types=1);

namespace App\DTOs\User;

use App\Enums\UserRole;

final readonly class CreateUserData
{
    public function __construct(
        public string $name,
        public string $username,
        public ?string $email,
        public string $password,
        public UserRole $role,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            username: strtoupper(trim((string) $data['username'])),
            email: filled($data['email'] ?? null) ? (string) $data['email'] : null,
            password: (string) $data['password'],
            role: UserRole::from((string) $data['role']),
        );
    }
}
