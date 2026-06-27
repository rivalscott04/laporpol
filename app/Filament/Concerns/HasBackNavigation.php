<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

trait HasBackNavigation
{
    protected function makeBackAction(?string $label = null, ?string $url = null): Action
    {
        $url ??= $this->getBackNavigationUrl();

        return Action::make('back')
            ->label($label ?? 'Kembali')
            ->icon(Heroicon::OutlinedArrowLeft)
            ->color('gray')
            ->url($url);
    }

    protected function getBackNavigationUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
