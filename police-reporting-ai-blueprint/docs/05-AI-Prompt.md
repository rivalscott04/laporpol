# Master Prompt

Build the application following all markdown documents.

Requirements:
- Laravel 12
- Filament 4
- Livewire 3

Architecture:
Controller -> FormRequest -> Service -> Repository -> Model.

Implement modules one by one.
Never place business logic in controllers.
Use policies, eager loading, DTOs, transactions and Storage.
Photo watermark must include date, time, latitude, longitude and location name.
