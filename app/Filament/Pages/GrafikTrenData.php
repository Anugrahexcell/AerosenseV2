<?php

namespace App\Filament\Pages;

use App\Models\Faculty;
use App\Models\SensorReading;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class GrafikTrenData extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $title           = 'Grafik Tren Data';
    protected static ?string $navigationLabel = 'Grafik Tren';
    protected static ?int    $navigationSort  = 3;
    protected string $view = 'filament.pages.grafik-tren-data';

    // ── Filters ──────────────────────────────────────────────────
    public ?int $faculty_id = null;
    public string $period   = '7';   // days

    public function mount(): void
    {
        $this->faculty_id = Faculty::orderBy('name')->value('id');
    }

    public function getFacultyOptions(): array
    {
        return Faculty::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getPeriodOptions(): array
    {
        return [
            '1'  => '24 Jam Terakhir',
            '7'  => '7 Hari Terakhir',
            '14' => '14 Hari Terakhir',
            '30' => '30 Hari Terakhir',
        ];
    }

    // ── Chart JSON consumed by Chart.js inline script ────────────
    public function getChartData(): array
    {
        $readings = SensorReading::query()
            ->when($this->faculty_id, fn ($q) => $q->where('faculty_id', $this->faculty_id))
            ->where('recorded_at', '>=', now()->subDays((int)$this->period))
            ->orderBy('recorded_at')
            ->limit(60)
            ->get();

        if ($readings->isEmpty()) {
            return ['labels' => [], 'temperature' => [], 'humidity' => [], 'co2' => []];
        }

        return [
            'labels'      => $readings->map(fn ($r) => $r->recorded_at->format('d/m H:i'))->toArray(),
            'temperature' => $readings->pluck('temperature')->map(fn ($v) => round($v, 1))->toArray(),
            'humidity'    => $readings->pluck('humidity')->map(fn ($v) => round($v, 1))->toArray(),
            'co2'         => $readings->pluck('co2')->map(fn ($v) => round($v, 1))->toArray(),
        ];
    }

    // ── Latest stats for selected faculty ────────────────────────
    public function getLatestStats(): array
    {
        $reading = SensorReading::query()
            ->when($this->faculty_id, fn ($q) => $q->where('faculty_id', $this->faculty_id))
            ->latest('recorded_at')
            ->first();

        return [
            'temperature' => $reading ? number_format($reading->temperature, 1) : '—',
            'humidity'    => $reading ? number_format($reading->humidity, 1)    : '—',
            'co2'         => $reading ? number_format($reading->co2, 1)         : '—',
        ];
    }

    // ── Table query (scoped to selected faculty + period) ─────────
    protected function getTableQuery(): Builder
    {
        return SensorReading::query()
            ->with('faculty')
            ->when($this->faculty_id, fn ($q) => $q->where('faculty_id', $this->faculty_id))
            ->where('recorded_at', '>=', now()->subDays((int)$this->period))
            ->orderBy('recorded_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('recorded_at')
                ->label('Waktu')
                ->dateTime('d M Y H:i:s')
                ->sortable()
                ->searchable(),

            TextColumn::make('faculty.name')
                ->label('Fakultas')
                ->sortable(),

            TextColumn::make('temperature')
                ->label('Suhu (°C)')
                ->numeric(1)
                ->sortable()
                ->color('warning'),

            TextColumn::make('humidity')
                ->label('Kelembapan (%)')
                ->numeric(1)
                ->sortable()
                ->color('info'),

            TextColumn::make('co2')
                ->label('CO₂ (ppm)')
                ->numeric(1)
                ->sortable()
                ->color('success'),

            TextColumn::make('air_quality_status')
                ->label('Status')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'Baik'               => 'success',
                    'Sedang'             => 'warning',
                    'Tidak Sehat'        => 'danger',
                    'Sangat Tidak Sehat' => 'danger',
                    'Berbahaya'          => 'danger',
                    default              => 'gray',
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make()
                ->form($this->getSensorReadingFormSchema())
                ->iconButton(),

            DeleteAction::make()
                ->iconButton(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Data')
                ->icon('heroicon-m-plus')
                ->model(SensorReading::class)
                ->form($this->getSensorReadingFormSchema())
                ->mutateFormDataUsing(function (array $data): array {
                    // Default to selected faculty if not explicitly chosen in the form
                    $data['faculty_id'] ??= $this->faculty_id;
                    return $data;
                })
                ->successNotification(
                    Notification::make()->success()->title('Data berhasil ditambahkan')
                ),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            DeleteBulkAction::make(),
        ];
    }

    protected function getSensorReadingFormSchema(): array
    {
        return [
            Select::make('faculty_id')
                ->label('Fakultas')
                ->options(Faculty::orderBy('name')->pluck('name', 'id'))
                ->required()
                ->searchable(),

            TextInput::make('temperature')
                ->label('Suhu (°C)')
                ->numeric()
                ->required()
                ->minValue(-50)
                ->maxValue(100)
                ->step(0.1),

            TextInput::make('humidity')
                ->label('Kelembapan (%)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->maxValue(100)
                ->step(0.1),

            TextInput::make('co2')
                ->label('CO₂ (ppm)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->step(0.1),

            DateTimePicker::make('recorded_at')
                ->label('Waktu Pencatatan')
                ->required()
                ->default(now()),
        ];
    }
}
