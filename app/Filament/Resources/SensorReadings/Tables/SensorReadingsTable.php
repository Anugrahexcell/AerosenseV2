<?php

namespace App\Filament\Resources\SensorReadings\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Faculty;

class SensorReadingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Kelola Data')
            ->description('Tambah, edit, atau hapus data kualitas udara')
            ->columns([
                TextColumn::make('faculty.name')
                    ->label('Fakultas')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('recorded_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('temperature')
                    ->label('Suhu (°C)')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->color('warning'),

                TextColumn::make('humidity')
                    ->label('Kelembaban (%)')
                    ->numeric(decimalPlaces: 1)
                    ->sortable(),

                TextColumn::make('co2')
                    ->label('CO₂ (ppm)')
                    ->numeric(decimalPlaces: 1)
                    ->sortable(),
            ])
            ->defaultSort('recorded_at', 'desc')
            ->filters([
                SelectFilter::make('faculty_id')
                    ->label('Semua Fakultas')
                    ->options(fn () => Faculty::orderBy('name')->pluck('name', 'id'))
                    ->placeholder('Semua Fakultas'),
            ])
            ->filtersFormMaxHeight('300px')
            ->recordActions([
                EditAction::make()
                    ->icon('heroicon-m-pencil')
                    ->iconButton()
                    ->color('gray'),
                DeleteAction::make()
                    ->icon('heroicon-m-trash')
                    ->iconButton()
                    ->color('danger'),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Data')
                    ->icon('heroicon-m-plus')
                    ->color('primary'),
            ])
            ->emptyStateHeading('Belum Ada Data')
            ->emptyStateDescription('Klik "Tambah Data" untuk menambah data kualitas udara pertama.')
            ->emptyStateIcon('heroicon-o-circle-stack')
            ->striped();
    }
}
