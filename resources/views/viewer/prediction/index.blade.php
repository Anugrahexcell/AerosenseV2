@extends('layouts.viewer')

@php
    $pageTitle       = 'Prediksi — AeroSenseV2';
    $metaDescription = 'Prediksi kualitas udara berbasis AI untuk seluruh fakultas Universitas Diponegoro.';

    // Day labels for multi-day section
    $dayLabels = ['Hari Ini', 'Besok', '2 Hari', '3 Hari'];
@endphp

@section('content')

{{-- ============================================================
     SECTION 1 — PREDICTION HERO & MODEL INFO
     ============================================================ --}}
<section class="prediction-hero" id="prediction-hero" aria-labelledby="prediction-title">
    <div class="container">
        <h1 class="section-title" id="prediction-title">
            Prediksi <span class="text-accent">Kualitas Udara</span>
        </h1>
        <p class="section-subtitle">
            Sistem AI kami memprediksi kondisi kualitas udara untuk
            membantu Anda merencanakan aktivitas dengan lebih baik
        </p>

        {{-- ML Model Info Panel --}}
        <div class="model-info-panel" id="model-info-panel" role="region" aria-label="Informasi model prediksi">
            <div class="model-info-panel__icon" aria-hidden="true">🧠</div>
            <div class="model-info-panel__content">
                <h2>Model Prediksi Machine Learning</h2>
                <p>
                    Menggunakan algoritma {{ $modelInfo['algorithm'] }} yang dilatih dengan data
                    historis dari {{ config('aerosense.total_faculties') }} fakultas, sistem ini memprediksi
                    kualitas udara hingga {{ config('aerosense.prediction_hours') }} jam ke depan dengan
                    akurasi tinggi.
                </p>
                <div class="model-meta">
                    <div class="model-meta-item">
                        <span>📊</span>
                        <span>Akurasi: <strong>{{ $modelInfo['accuracy'] }}%</strong></span>
                    </div>
                    <div class="model-meta-sep" aria-hidden="true"></div>
                    <div class="model-meta-item">
                        <span>🕐</span>
                        <span>Update: <strong>Setiap jam</strong></span>
                    </div>
                    <div class="model-meta-sep" aria-hidden="true"></div>
                    <div class="model-meta-item">
                        <span>ℹ️</span>
                        <span>Model: <strong>{{ $modelInfo['name'] }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     SECTIONS 2, 3, 4 — All on dark background
     ============================================================ --}}
<div class="prediction-main">
    <div class="container">

        {{-- ========================================================
             SECTION 2 — PREDICTION PER FACULTY / LOCATION
             ======================================================== --}}
        <div class="pred-section" id="faculty-prediction-section">
            <h2 class="pred-section-title">
                <span class="icon">📍</span>
                Prediksi per Lokasi/Fakultas
            </h2>

            @if($facultyPredictions->isNotEmpty())
            <div class="prediction-grid" id="faculty-prediction-grid">
                @foreach($facultyPredictions as $facultyId => $predictions)
                @php $pred = $predictions->first(); @endphp
                <div class="prediction-card" id="pred-faculty-{{ $facultyId }}">
                    <div class="prediction-card__header">
                        <span class="prediction-card__name">{{ $pred->faculty->name ?? 'Fakultas' }}</span>
                        <span class="status-badge {{ $pred->status_class }}">{{ $pred->predicted_status }}</span>
                    </div>
                    <div class="pred-metrics">
                        <div class="pred-metric">
                            <span class="pred-metric__label">💨 CO₂</span>
                            <span class="pred-metric__value">{{ $pred->predicted_co2 }} ppm</span>
                        </div>
                        <div class="pred-metric">
                            <span class="pred-metric__label">🌡 Suhu</span>
                            <span class="pred-metric__value">{{ $pred->predicted_temperature }}°C</span>
                        </div>
                        <div class="pred-metric">
                            <span class="pred-metric__label">💧 Kelembapan</span>
                            <span class="pred-metric__value">{{ $pred->predicted_humidity }}%</span>
                        </div>
                    </div>
                    <div class="pred-confidence">
                        <span class="pred-confidence__label">Confidence Score</span>
                        <span class="pred-confidence__value">{{ $pred->confidence_score }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="chart-body-placeholder" style="background:rgba(255,255,255,.04); border-color:rgba(255,255,255,.1); color:var(--text-secondary);">
                <span class="placeholder-icon">🔮</span>
                <span style="font-size:.82rem;">Data prediksi belum tersedia. Jalankan seeder untuk melihat data.</span>
            </div>
            @endif
        </div>

        {{-- ========================================================
             SECTION 3 — HOURLY PREDICTION TODAY
             ======================================================== --}}
        <div class="pred-section" id="hourly-prediction-section">
            <h2 class="pred-section-title">
                <span class="icon">🕐</span>
                Prediksi Per Jam Hari Ini
            </h2>

            @if($hourlyPredictions->isNotEmpty())
            <div class="hourly-grid" id="hourly-prediction-grid">
                @foreach($hourlyPredictions as $hourly)
                <div class="hourly-card" id="hourly-{{ $loop->index }}">
                    <div class="hourly-card__time">
                        {{ \Carbon\Carbon::parse($hourly->predicted_for)->format('H:i') }}
                        &ndash;
                        {{ \Carbon\Carbon::parse($hourly->predicted_for)->addHours(3)->format('H:i') }}
                    </div>
                    <div class="hourly-metrics">
                        <div class="hourly-metric">
                            <span class="hourly-metric__label">CO₂</span>
                            <span class="hourly-metric__value">{{ $hourly->predicted_co2 }} ppm</span>
                        </div>
                        <div class="hourly-metric">
                            <span class="hourly-metric__label">Suhu</span>
                            <span class="hourly-metric__value">{{ $hourly->predicted_temperature }}°C</span>
                        </div>
                        <div class="hourly-metric">
                            <span class="hourly-metric__label">Kelembapan</span>
                            <span class="hourly-metric__value">{{ $hourly->predicted_humidity }}%</span>
                        </div>
                    </div>
                    <span class="status-badge {{ $hourly->status_class }}">{{ $hourly->predicted_status }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="hourly-grid" id="hourly-prediction-grid">
                @foreach([['12:00','15:00',420,28,65,'Baik','baik'],['15:00','18:00',450,29,62,'Baik','baik'],['18:00','21:00',485,30,66,'Sedang','sedang'],['21:00','24:00',435,27,70,'Baik','baik']] as $i => $slot)
                <div class="hourly-card" id="hourly-slot-{{ $i }}">
                    <div class="hourly-card__time">{{ $slot[0] }} &ndash; {{ $slot[1] }}</div>
                    <div class="hourly-metrics">
                        <div class="hourly-metric">
                            <span class="hourly-metric__label">CO₂</span>
                            <span class="hourly-metric__value">{{ $slot[2] }} ppm</span>
                        </div>
                        <div class="hourly-metric">
                            <span class="hourly-metric__label">Suhu</span>
                            <span class="hourly-metric__value">{{ $slot[3] }}°C</span>
                        </div>
                        <div class="hourly-metric">
                            <span class="hourly-metric__label">Kelembapan</span>
                            <span class="hourly-metric__value">{{ $slot[4] }}%</span>
                        </div>
                    </div>
                    <span class="status-badge {{ $slot[6] }}">{{ $slot[5] }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ========================================================
             SECTION 4 — MULTI-DAY PREDICTION (3 days ahead)
             ======================================================== --}}
        <div class="pred-section" id="multiday-prediction-section">
            <h2 class="pred-section-title">
                <span class="icon">📅</span>
                Prediksi 3 Hari Ke Depan
            </h2>

            @if($dailyPredictions->isNotEmpty())
            <div class="multiday-grid" id="multiday-prediction-grid">
                @foreach($dailyPredictions as $index => $daily)
                <div class="multiday-card" id="multiday-day-{{ $index }}">
                    <div class="multiday-card__day">{{ $dayLabels[$index] ?? \Carbon\Carbon::parse($daily->predicted_for)->format('d M') }}</div>
                    <div class="multiday-metrics">
                        <div class="multiday-metric">
                            <span class="multiday-metric__label">CO₂</span>
                            <span class="multiday-metric__value">{{ $daily->predicted_co2 }} ppm</span>
                        </div>
                        <div class="multiday-metric">
                            <span class="multiday-metric__label">Suhu</span>
                            <span class="multiday-metric__value">{{ $daily->predicted_temperature }}°C</span>
                        </div>
                        <div class="multiday-metric">
                            <span class="multiday-metric__label">Kelembapan</span>
                            <span class="multiday-metric__value">{{ $daily->predicted_humidity }}%</span>
                        </div>
                    </div>
                    <div class="multiday-card__footer">
                        <span class="status-badge {{ $daily->status_class }}">{{ $daily->predicted_status }}</span>
                        <span class="trend-arrow stable" aria-label="Tren stabil">&#8212;</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            {{-- Fallback static dummy cards matching UI --}}
            <div class="multiday-grid" id="multiday-prediction-grid">
                @foreach([['Hari Ini',435,28,66,'Baik','baik'],['Besok',470,29,68,'Sedang','sedang'],['2 Hari',445,28,65,'Baik','baik'],['3 Hari',420,27,64,'Baik','baik']] as $i => $day)
                <div class="multiday-card" id="multiday-static-{{ $i }}">
                    <div class="multiday-card__day">{{ $day[0] }}</div>
                    <div class="multiday-metrics">
                        <div class="multiday-metric">
                            <span class="multiday-metric__label">CO₂</span>
                            <span class="multiday-metric__value">{{ $day[1] }} ppm</span>
                        </div>
                        <div class="multiday-metric">
                            <span class="multiday-metric__label">Suhu</span>
                            <span class="multiday-metric__value">{{ $day[2] }}°C</span>
                        </div>
                        <div class="multiday-metric">
                            <span class="multiday-metric__label">Kelembapan</span>
                            <span class="multiday-metric__value">{{ $day[3] }}%</span>
                        </div>
                    </div>
                    <div class="multiday-card__footer">
                        <span class="status-badge {{ $day[5] }}">{{ $day[4] }}</span>
                        <span class="trend-arrow stable" aria-hidden="true">&#8212;</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>

@endsection
