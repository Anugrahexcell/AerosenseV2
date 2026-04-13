<?php

namespace Database\Seeders;

use App\Models\AirQualityPrediction;
use App\Models\Faculty;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AirQualityPredictionSeeder extends Seeder
{
    public function run(): void
    {
        $today     = Carbon::today();
        $faculties = Faculty::all();

        $facultySamples = [
            ['co2' => 420, 'temp' => 28, 'hum' => 65, 'status' => 'Baik',    'conf' => 94],
            ['co2' => 405, 'temp' => 27, 'hum' => 68, 'status' => 'Baik',    'conf' => 92],
            ['co2' => 485, 'temp' => 29, 'hum' => 70, 'status' => 'Sedang',  'conf' => 89],
            ['co2' => 395, 'temp' => 26, 'hum' => 62, 'status' => 'Baik',    'conf' => 96],
            ['co2' => 520, 'temp' => 30, 'hum' => 72, 'status' => 'Sedang',  'conf' => 87],
            ['co2' => 580, 'temp' => 31, 'hum' => 75, 'status' => 'Tidak Sehat', 'conf' => 81],
        ];

        // 1. Per-faculty daily predictions (for the faculty cards section)
        foreach ($faculties->take(6) as $index => $faculty) {
            $s = $facultySamples[$index];
            AirQualityPrediction::firstOrCreate(
                ['faculty_id' => $faculty->id, 'prediction_type' => 'daily', 'predicted_for' => $today],
                [
                    'predicted_co2'         => $s['co2'],
                    'predicted_temperature' => $s['temp'],
                    'predicted_humidity'    => $s['hum'],
                    'predicted_status'      => $s['status'],
                    'confidence_score'      => $s['conf'],
                    'model_version'         => 'AeroSense v2',
                    'generated_at'          => now(),
                ]
            );
        }

        // 2. Campus-wide hourly predictions for today (for the hourly section)
        $hourlyData = [
            ['time' => '12:00', 'co2' => 420, 'temp' => 28, 'hum' => 65, 'status' => 'Baik'],
            ['time' => '15:00', 'co2' => 450, 'temp' => 29, 'hum' => 62, 'status' => 'Baik'],
            ['time' => '18:00', 'co2' => 485, 'temp' => 30, 'hum' => 66, 'status' => 'Sedang'],
            ['time' => '21:00', 'co2' => 435, 'temp' => 27, 'hum' => 70, 'status' => 'Baik'],
        ];

        foreach ($hourlyData as $h) {
            [$hour, $minute] = explode(':', $h['time']);
            $predictedFor = $today->copy()->setHour((int)$hour)->setMinute(0)->setSecond(0);
            AirQualityPrediction::firstOrCreate(
                ['faculty_id' => null, 'prediction_type' => 'hourly', 'predicted_for' => $predictedFor],
                [
                    'predicted_co2'         => $h['co2'],
                    'predicted_temperature' => $h['temp'],
                    'predicted_humidity'    => $h['hum'],
                    'predicted_status'      => $h['status'],
                    'confidence_score'      => 88,
                    'model_version'         => 'AeroSense v2',
                    'generated_at'          => now(),
                ]
            );
        }

        // 3. Campus-wide daily predictions for today + 3 upcoming days (multi-day section)
        $dailyData = [
            ['offset' => 0, 'co2' => 435, 'temp' => 28, 'hum' => 66, 'status' => 'Baik'],
            ['offset' => 1, 'co2' => 470, 'temp' => 29, 'hum' => 68, 'status' => 'Sedang'],
            ['offset' => 2, 'co2' => 445, 'temp' => 28, 'hum' => 65, 'status' => 'Baik'],
            ['offset' => 3, 'co2' => 420, 'temp' => 27, 'hum' => 64, 'status' => 'Baik'],
        ];

        foreach ($dailyData as $d) {
            $predictedFor = $today->copy()->addDays($d['offset']);
            AirQualityPrediction::firstOrCreate(
                ['faculty_id' => null, 'prediction_type' => 'daily', 'predicted_for' => $predictedFor],
                [
                    'predicted_co2'         => $d['co2'],
                    'predicted_temperature' => $d['temp'],
                    'predicted_humidity'    => $d['hum'],
                    'predicted_status'      => $d['status'],
                    'confidence_score'      => 85,
                    'model_version'         => 'AeroSense v2',
                    'generated_at'          => now(),
                ]
            );
        }
    }
}
