<?php

declare(strict_types=1);

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

abstract class ReportFormRequest extends FormRequest
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
    protected function coordinateRules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function attachmentRules(bool $required = false): array
    {
        return $required
            ? ['required', 'string']
            : ['nullable', 'string'];
    }
}
