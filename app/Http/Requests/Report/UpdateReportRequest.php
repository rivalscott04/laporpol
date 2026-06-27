<?php

declare(strict_types=1);

namespace App\Http\Requests\Report;

class UpdateReportRequest extends ReportFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reported_at' => ['required', 'date'],
            'location_name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'string'],
            'attachment' => $this->attachmentRules(),
            ...$this->coordinateRules(),
        ];
    }
}
