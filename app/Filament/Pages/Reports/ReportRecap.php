<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\DTOs\Report\ReportRecapData;
use App\Enums\Permission;
use App\Enums\ReportRecapPeriod;
use App\Filament\Concerns\CanExportReports;
use App\Filament\Exports\ReportExporter;
use App\Filament\Resources\Reports\Tables\ReportsTable;
use App\Models\Report;
use App\Services\ReportPdfExportService;
use App\Services\ReportRecapService;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ReportRecap extends Page implements HasTable
{
    use CanExportReports;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Rekap Laporan';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'reports/recap';

    #[Url]
    public string $period = 'daily';

    #[Url]
    public ?string $referenceDate = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->can(Permission::ReportsRecap->value);
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $this->referenceDate ??= CarbonImmutable::today()->toDateString();

        if (ReportRecapPeriod::tryFrom($this->period) === null) {
            $this->period = ReportRecapPeriod::Daily->value;
        }
    }

    public function getTitle(): string|Htmlable
    {
        return 'Rekap Laporan';
    }

    public function getSubheading(): string|Htmlable|null
    {
        $recap = $this->getRecap();

        return sprintf('%s · Total %d laporan', $recap->periodLabel, $recap->total);
    }

    public function updatedPeriod(): void
    {
        if (ReportRecapPeriod::tryFrom($this->period) === null) {
            return;
        }

        $this->resetTable();
    }

    public function updatedReferenceDate(): void
    {
        if (blank($this->referenceDate)) {
            return;
        }

        $this->resetTable();
    }

    /**
     * @return array<int, mixed>
     */
    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Unduh Excel')
                ->icon(Heroicon::OutlinedTableCells)
                ->exporter(ReportExporter::class)
                ->visible(fn (): bool => $this->canExportReports())
                ->disabled(fn (): bool => ! $this->hasExportableReportRecords())
                ->tooltip(fn (): ?string => $this->reportExportDisabledTooltip()),
            Action::make('exportPdf')
                ->label('Unduh PDF')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->action(fn (): mixed => $this->exportRecapPdf())
                ->visible(fn (): bool => $this->canExportReports())
                ->disabled(fn (): bool => ! $this->hasExportableReportRecords())
                ->tooltip(fn (): ?string => $this->reportExportDisabledTooltip()),
        ];
    }

    public function exportRecapPdf(): mixed
    {
        abort_unless($this->canExportReports(), 403);
        abort_unless($this->hasExportableReportRecords(), 404);

        $recap = $this->getRecap();

        return app(ReportPdfExportService::class)->download(
            $this->getTableQueryForExport(),
            'Rekap Laporan: '.$recap->periodLabel,
            'rekap-laporan-'.now()->format('Y-m-d-His').'.pdf',
        );
    }

    public function table(Table $table): Table
    {
        return ReportsTable::configureForRecap($table);
    }

    protected function getTableQuery(): Builder
    {
        $recap = $this->getRecap();

        return Report::query()
            ->with('user')
            ->whereDate('reported_at', '>=', $recap->start->toDateString())
            ->whereDate('reported_at', '<=', $recap->end->toDateString())
            ->orderByDesc('reported_at');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Periode Rekap')
                    ->description('Pilih jenis rekap dan tanggal yang ingin dilihat. Rekap mingguan dihitung dari Senin sampai Minggu.')
                    ->schema([
                        Select::make('period')
                            ->label('Jenis Rekap')
                            ->options(collect(ReportRecapPeriod::cases())->mapWithKeys(
                                fn (ReportRecapPeriod $period): array => [$period->value => $period->label()],
                            )->all())
                            ->default(ReportRecapPeriod::Daily->value)
                            ->selectablePlaceholder(false)
                            ->required()
                            ->live(),
                        DatePicker::make('referenceDate')
                            ->label('Tanggal Periode')
                            ->required()
                            ->live()
                            ->native(false)
                            ->displayFormat('d M Y'),
                    ])
                    ->columns(2),
                EmbeddedTable::make(),
            ]);
    }

    private function getRecap(): ReportRecapData
    {
        return app(ReportRecapService::class)->recap(
            $this->getRecapPeriod(),
            CarbonImmutable::parse($this->referenceDate ?? CarbonImmutable::today()->toDateString()),
        );
    }

    private function getRecapPeriod(): ReportRecapPeriod
    {
        return ReportRecapPeriod::tryFrom($this->period) ?? ReportRecapPeriod::Daily;
    }
}
