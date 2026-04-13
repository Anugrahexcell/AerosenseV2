@extends('layouts.viewer')

@php
    $pageTitle       = 'Dashboard — AeroSenseV2';
    $metaDescription = 'Pantau kualitas udara real-time di seluruh fakultas Universitas Diponegoro.';
@endphp

@section('content')

{{-- ============================================================
     SECTION 1 — HERO / INTRODUCTION
     ============================================================ --}}
<section class="hero" id="hero-section" aria-labelledby="hero-title">
    {{-- Campus background image overlay (sits behind content via CSS absolute positioning) --}}
    <div class="hero__bg" aria-hidden="true"></div>
    <div class="container">
        <h1 class="hero__title" id="hero-title">
            <span class="title-main">AeroSense</span>
            Air Quality Dashboard
        </h1>
        <p class="hero__desc">
            AeroSense menyajikan wawasan kualitas udara yang komprehensif
            di seluruh area kampus dengan prediksi berbasis AI, membantu
            sivitas akademika tetap terinformasi dan menjaga kesehatan.
        </p>

        {{-- Summary Stat Cards --}}
        <div class="hero__stats" id="hero-stats">
            <div class="stat-card" id="stat-faculties">
                <div class="stat-card__value">{{ $facultyCount ?: config('aerosense.total_faculties') }}</div>
                <div class="stat-card__label">Fakultas</div>
            </div>
            <div class="stat-card" id="stat-monitoring">
                <div class="stat-card__value">24/7</div>
                <div class="stat-card__label">Monitoring</div>
            </div>
            <div class="stat-card" id="stat-ai">
                <div class="stat-card__value">AI</div>
                <div class="stat-card__label">Powered Prediction</div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     SECTION 2 — REAL-TIME AIR QUALITY
     Note: Data comes from sensor_readings table (seeded for now).
     Phase 3 will add JavaScript polling every 30 seconds.
     ============================================================ --}}
<section class="realtime-section" id="realtime-section" aria-labelledby="realtime-title">
    <div class="container">
        <h2 class="section-title" id="realtime-title">Data Kualitas Udara Real&#8209;Time</h2>
        <p class="section-subtitle">
            Pantau kondisi udara terkini di berbagai fakultas Universitas Diponegoro
        </p>

        @if($sensorReadings->isNotEmpty())
        <div class="realtime-grid" id="realtime-grid">

            {{-- Left: first 2 cards --}}
            <div class="realtime-grid__cards-left">
                @foreach($sensorReadings->take(2) as $reading)
                <div class="faculty-card" id="faculty-card-{{ $reading->faculty_id }}">
                    <div class="faculty-card__name">{{ $reading->faculty->name }}</div>
                    <div class="faculty-card__status {{ $reading->status_class }}">
                        {{ $reading->air_quality_status }}
                    </div>
                    <div class="faculty-card__metrics">
                        <div class="metric-item">
                            <span>💨 CO₂</span>
                            <strong>{{ $reading->co2 }} ppm</strong>
                        </div>
                        <div class="metric-item">
                            <span>🌡 Suhu</span>
                            <strong>{{ $reading->temperature }}°C</strong>
                        </div>
                        <div class="metric-item">
                            <span>💧 Kelembapan</span>
                            <strong>{{ $reading->humidity }}%</strong>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Center: Globe Illustration --}}
            <div class="realtime-grid__center" aria-hidden="true">
                <div class="globe-visual">🌍</div>
            </div>

            {{-- Right: next 2 cards --}}
            <div class="realtime-grid__cards-right">
                @foreach($sensorReadings->skip(2)->take(2) as $reading)
                <div class="faculty-card" id="faculty-card-{{ $reading->faculty_id }}">
                    <div class="faculty-card__name">{{ $reading->faculty->name }}</div>
                    <div class="faculty-card__status {{ $reading->status_class }}">
                        {{ $reading->air_quality_status }}
                    </div>
                    <div class="faculty-card__metrics">
                        <div class="metric-item">
                            <span>💨 CO₂</span>
                            <strong>{{ $reading->co2 }} ppm</strong>
                        </div>
                        <div class="metric-item">
                            <span>🌡 Suhu</span>
                            <strong>{{ $reading->temperature }}°C</strong>
                        </div>
                        <div class="metric-item">
                            <span>💧 Kelembapan</span>
                            <strong>{{ $reading->humidity }}%</strong>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
        @else
        <div class="chart-body-placeholder" style="margin-top: 1rem;">
            <span class="placeholder-icon">📡</span>
            <span>Data sensor belum tersedia. Jalankan seeder untuk melihat data.</span>
        </div>
        @endif
    </div>
</section>

{{-- ============================================================
     SECTION 3 — HISTORICAL AIR QUALITY TREND
     Note: This section is NOT real-time.
     Data is manually managed by admin via air_quality_trends table.
     Chart.js integration will be added in Phase 3.
     ============================================================ --}}
<section class="trend-section" id="trend-section" aria-labelledby="trend-title">
    <div class="container">
        <h2 class="section-title" id="trend-title">Tren Kualitas Udara Historis</h2>
        <p class="section-subtitle">
            Analisis data historis untuk memahami pola<br>
            dan perubahan kualitas udara dari waktu ke waktu
        </p>

        <div class="chart-container" id="chart-container">
            <div class="chart-header">
                <div class="chart-title-group">
                    <span class="chart-icon">📊</span>
                    <div class="chart-title-text">
                        <strong>Grafik tren &ndash; 30 Terakhir</strong>
                        <span>Rata-rata indeks kualitas udara di semua fakultas</span>
                    </div>
                </div>
                <div class="chart-date-tag">
                    <span>📅</span>
                    <span id="chart-date-range">
                        {{ now()->subDays(29)->format('d M Y') }} &ndash; {{ now()->format('d M Y') }}
                    </span>
                </div>
            </div>

            {{-- Placeholder: Chart.js canvas will be placed here in Phase 3 --}}
            <div class="chart-body-placeholder" id="chart-placeholder">
                <span class="placeholder-icon">📈</span>
                <span>Chart.js akan diintegrasikan pada Phase 3</span>
                <small style="font-size:.7rem; opacity:.6;">
                    Data siap: {{ $trendData->count() }} entri historis tersedia
                </small>
            </div>

            <div class="chart-legend" aria-label="Chart legend">
                <div class="legend-item">
                    <div class="legend-dot" style="background:#ef4444;"></div>
                    <span>CO Level</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#f97316;"></div>
                    <span>Temperatur</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#00d4a0;"></div>
                    <span>Co2</span>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
