<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalculationController;

Route::get('/', function () {
    return view('calculation');
});

Route::post('/api/submit', [CalculationController::class, 'calculate']);

