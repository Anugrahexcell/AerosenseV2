<x-filament-panels::page>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <x-filament::section>
        <div style="display:flex; flex-direction:row; align-items:flex-end; gap:1rem; flex-wrap:nowrap;">

            <div style="flex:2; min-width:0;">
                <label style="display:block; font-size:0.8rem; font-weight:500; margin-bottom:0.3rem; color:#6b7280;">Fakultas</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="faculty_id">
                        @foreach($this->getFacultyOptions() as $id => $name)
                            <option value="{{ $id }}" @selected($faculty_id == $id)>{{ $name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div style="flex:1.5; min-width:0;">
                <label style="display:block; font-size:0.8rem; font-weight:500; margin-bottom:0.3rem; color:#6b7280;">Periode</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="period">
                        @foreach($this->getPeriodOptions() as $val => $label)
                            <option value="{{ $val }}" @selected($period === $val)>{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </div>
    </x-filament::section>

    {{-- ── Latest Stats Cards ───────────────────────────────────── --}}
    @php $stats = $this->getLatestStats(); @endphp
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem;">
        <x-filament::section>
            <div style="border-left:4px solid #f97316; padding-left:0.75rem;">
                <div style="display:flex; align-items:center; gap:0.4rem; color:#6b7280; font-size:0.82rem; margin-bottom:0.4rem;">
                    <x-heroicon-m-fire style="width:14px;height:14px;color:#f97316;" /> Suhu Terkini (°C)
                </div>
                <div style="font-size:2rem; font-weight:700;">{{ $stats['temperature'] }}</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="border-left:4px solid #3b82f6; padding-left:0.75rem;">
                <div style="display:flex; align-items:center; gap:0.4rem; color:#6b7280; font-size:0.82rem; margin-bottom:0.4rem;">
                    <x-heroicon-m-cloud style="width:14px;height:14px;color:#3b82f6;" /> Kelembapan Terkini (%)
                </div>
                <div style="font-size:2rem; font-weight:700;">{{ $stats['humidity'] }}</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div style="border-left:4px solid #22c55e; padding-left:0.75rem;">
                <div style="display:flex; align-items:center; gap:0.4rem; color:#6b7280; font-size:0.82rem; margin-bottom:0.4rem;">
                    <x-heroicon-m-sparkles style="width:14px;height:14px;color:#22c55e;" /> CO₂ Terkini (ppm)
                </div>
                <div style="font-size:2rem; font-weight:700;">{{ $stats['co2'] }}</div>
            </div>
        </x-filament::section>
    </div>

    {{-- ── Chart.js Line Chart ──────────────────────────────────── --}}
    <x-filament::section>
        <div style="padding:0.25rem 0 0.5rem;">
            <canvas id="trendChart" style="width:100%; max-height:360px;"></canvas>
        </div>
    </x-filament::section>

    {{-- ── CRUD Data Table ──────────────────────────────────────── --}}
    <x-filament::section heading="Manajemen Data Sensor" description="Tambah, edit, atau hapus data sensor readings yang ditampilkan pada grafik.">
        {{ $this->table }}
    </x-filament::section>

    {{-- ── Chart.js Script ─────────────────────────────────────── --}}
    @php $chartData = $this->getChartData(); @endphp
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            const labels      = @json($chartData['labels']);
            const temperature = @json($chartData['temperature']);
            const humidity    = @json($chartData['humidity']);
            const co2         = @json($chartData['co2']);

            function buildChart() {
                const canvas = document.getElementById('trendChart');
                if (!canvas) return;

                // Destroy existing instance if Livewire re-rendered
                if (canvas._chartInstance) {
                    canvas._chartInstance.destroy();
                }

                canvas._chartInstance = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Suhu (°C)',
                                data: temperature,
                                borderColor: '#f97316',
                                backgroundColor: 'rgba(249,115,22,0.12)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 3,
                            },
                            {
                                label: 'Kelembapan (%)',
                                data: humidity,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59,130,246,0.12)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 3,
                            },
                            {
                                label: 'CO₂ (ppm)',
                                data: co2,
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34,197,94,0.12)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 3,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: { callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}` } },
                        },
                        scales: {
                            x: { ticks: { maxTicksLimit: 10, maxRotation: 45 } },
                            y: { beginAtZero: false },
                        },
                    },
                });
            }

            // Initial render
            document.addEventListener('DOMContentLoaded', buildChart);

            // Re-render after each Livewire update (handles wire:poll + filter changes)
            document.addEventListener('livewire:navigated', buildChart);
            window.addEventListener('livewire:update', () => setTimeout(buildChart, 100));
        })();
    </script>

    <style>
        @keyframes aeroPulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.25; }
        }
    </style>

</x-filament-panels::page>
