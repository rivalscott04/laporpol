<?php

declare(strict_types=1);

namespace App\DTOs\User;

final readonly class UpdateProfileData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            email: (string) $data['email'],
            password: filled($data['password'] ?? null) ? (string) $data['password'] : null,
        );
    }
}
