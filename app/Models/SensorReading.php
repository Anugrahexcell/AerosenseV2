<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    public $timestamps = false; // uses recorded_at + created_at manually

    protected $fillable = [
        'faculty_id',
        'co2',
        'temperature',
        'humidity',
        'air_quality_status',
        'aqi_index',
        'recorded_at',
    ];

    protected $casts = [
        'co2'         => 'float',
        'temperature' => 'float',
        'humidity'    => 'float',
        'aqi_index'   => 'integer',
        'recorded_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    // ── Scopes ─────────────────────────────────────────────────

    /**
     * Returns the latest reading per faculty using a subquery.
     * Used by DashboardController to build the real-time grid.
     */
    public function scopeLatestPerFaculty(Builder $query): Builder
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('sensor_readings')
                ->groupBy('faculty_id');
        })->orderBy('faculty_id');
    }

    // ── Accessors ──────────────────────────────────────────────

    /**
     * Returns a CSS class name matching the air quality status.
     * Used directly in Blade to apply card border/text colours.
     */
    public function getStatusClassAttribute(): string
    {
        return match ($this->air_quality_status) {
            'Baik'                => 'baik',
            'Sedang'              => 'sedang',
            'Tidak Sehat'         => 'tidak-sehat',
            'Sangat Tidak Sehat'  => 'sangat-tidak-sehat',
            'Berbahaya'           => 'berbahaya',
            default               => 'baik',
        };
    }
}
