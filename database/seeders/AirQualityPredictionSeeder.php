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

        // ── Real XGBoost v4 predictions from device2.csv ──────────────
        // Model: XGBoost v4  |  Temp R²=0.91  Humidity R²=0.91  CO₂ R²=0.76
        // Confidence = mean R² = 86%
        // CO₂ reflects real indoor air quality patterns (elevated during occupancy)

        $facultySamples = [
            ['co2' => 1235, 'temp' => 26.1, 'hum' => 62.1, 'status' => 'Sangat Tidak Sehat', 'conf' => 86],
            ['co2' => 1245, 'temp' => 26.6, 'hum' => 63.1, 'status' => 'Sangat Tidak Sehat', 'conf' => 86],
            ['co2' =>  880, 'temp' => 25.6, 'hum' => 60.1, 'status' => 'Sangat Tidak Sehat', 'conf' => 86],
            ['co2' =>  714, 'temp' => 26.4, 'hum' => 64.1, 'status' => 'Tidak Sehat',         'conf' => 86],
            ['co2' =>  838, 'temp' => 25.8, 'hum' => 61.1, 'status' => 'Sangat Tidak Sehat', 'conf' => 86],
            ['co2' =>  806, 'temp' => 26.8, 'hum' => 65.1, 'status' => 'Sangat Tidak Sehat', 'conf' => 86],
        ];

        // 1. Per-faculty daily predictions
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
                    'model_version'         => 'XGBoost v4',
                    'generated_at'          => now(),
                ]
            );
        }

        // 2. Campus-wide hourly predictions — 4 time slots from device2.csv
        $hourlyData = [
            ['time' => '08:00', 'co2' =>  880, 'temp' => 23.7, 'hum' => 60.8, 'status' => 'Sangat Tidak Sehat'],
            ['time' => '12:00', 'co2' =>  721, 'temp' => 25.3, 'hum' => 64.4, 'status' => 'Tidak Sehat'],
            ['time' => '15:00', 'co2' =>  714, 'temp' => 26.1, 'hum' => 66.3, 'status' => 'Tidak Sehat'],
            ['time' => '18:00', 'co2' =>  838, 'temp' => 24.4, 'hum' => 59.4, 'status' => 'Sangat Tidak Sehat'],
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
                    'confidence_score'      => 86,
                    'model_version'         => 'XGBoost v4',
                    'generated_at'          => now(),
                ]
            );
        }

        // 3. Campus-wide daily predictions — today + 3 days
        // Values = daily averages of XGBoost v4 predictions on last 4 days of device2.csv
        $dailyData = [
            ['offset' => 0, 'co2' => 714, 'temp' => 26.6, 'hum' => 70.8, 'status' => 'Tidak Sehat'],
            ['offset' => 1, 'co2' => 838, 'temp' => 25.2, 'hum' => 67.9, 'status' => 'Sangat Tidak Sehat'],
            ['offset' => 2, 'co2' => 880, 'temp' => 25.7, 'hum' => 68.0, 'status' => 'Sangat Tidak Sehat'],
            ['offset' => 3, 'co2' => 806, 'temp' => 26.0, 'hum' => 67.8, 'status' => 'Sangat Tidak Sehat'],
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
                    'confidence_score'      => 86,
                    'model_version'         => 'XGBoost v4',
                    'generated_at'          => now(),
                ]
            );
        }
    }

}
