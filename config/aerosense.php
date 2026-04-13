<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Identity
    |--------------------------------------------------------------------------
    */
    'name'    => 'AeroSenseV2',
    'version' => '2.0',
    'university' => 'Universitas Diponegoro',

    /*
    |--------------------------------------------------------------------------
    | Monitoring Stats (shown on Dashboard hero)
    |--------------------------------------------------------------------------
    */
    'total_faculties'  => 13,
    'monitoring_hours' => '24/7',
    'model_accuracy'   => 86,
    'model_name'       => 'AeroSense v2',
    'model_algorithm'  => 'Random Forest',
    'prediction_hours' => 24,

    /*
    |--------------------------------------------------------------------------
    | Air Quality Status Thresholds (CO₂ in ppm)
    |--------------------------------------------------------------------------
    */
    'thresholds' => [
        'baik'             => [0,    450],
        'sedang'           => [451,  600],
        'tidak_sehat'      => [601,  800],
        'sangat_tidak_sehat' => [801, 1000],
        'berbahaya'        => [1001, PHP_INT_MAX],
    ],

    /*
    |--------------------------------------------------------------------------
    | Real-Time Polling Interval (seconds) — used by JavaScript
    |--------------------------------------------------------------------------
    */
    'polling_interval' => 30,

    /*
    |--------------------------------------------------------------------------
    | Historical Trend Default Range (days)
    |--------------------------------------------------------------------------
    */
    'trend_days' => 30,

];
