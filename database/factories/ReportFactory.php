<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reported_at' => CarbonImmutable::today(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'location_name' => fake()->streetAddress(),
            'photo_path' => null,
            'attachment_path' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    public function reportedOn(CarbonImmutable $date): static
    {
        return $this->state(fn (array $attributes): array => [
            'reported_at' => $date->toDateString(),
        ]);
    }
}
