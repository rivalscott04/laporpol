<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Enums\Permission;
use App\Services\SettingService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class ManageReportUploadSettings extends Page
{
    use CanUseDatabaseTransactions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = 'Batas Unggahan Laporan';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 99;

    protected static ?string $slug = 'settings/report-upload';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->can(Permission::UsersView->value);
    }

    public function mount(SettingService $settingService): void
    {
        abort_unless(static::canAccess(), 403);

        $this->form->fill([
            'photo_max_mb' => (int) round($settingService->photoMaxKb() / 1024),
            'attachment_max_mb' => (int) round($settingService->attachmentMaxKb() / 1024),
        ]);
    }

    public function save(SettingService $settingService): void
    {
        abort_unless(static::canAccess(), 403);

        $data = $this->form->getState();

        $settingService->updateReportUploadSettings([
            'photo_max_kb' => (int) $data['photo_max_mb'] * 1024,
            'attachment_max_kb' => (int) $data['attachment_max_mb'] * 1024,
        ]);

        Notification::make()
            ->title('Pengaturan batas unggahan berhasil disimpan')
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return 'Batas Unggahan Laporan';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Batas Ukuran Unggahan Laporan';
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ukuran File yang Diizinkan')
                    ->description('Atur seberapa besar foto dan lampiran boleh diunggah, dalam satuan MB (megabyte).')
                    ->schema([
                        TextInput::make('photo_max_mb')
                            ->label('Ukuran Maksimal Foto')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(10)
                            ->suffix('MB')
                            ->helperText('Contoh: isi 1 jika batasnya 1 MB.'),
                        TextInput::make('attachment_max_mb')
                            ->label('Ukuran Maksimal Lampiran PDF')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(10)
                            ->suffix('MB')
                            ->helperText('Contoh: isi 1 jika batasnya 1 MB.'),
                    ])
                    ->columns(2),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->key('form-actions'),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::Start;
    }
}
