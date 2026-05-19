<?php

declare(strict_types=1);

use App\Http\Controllers\DriverController;
use App\Http\Controllers\RaceController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StandingsController;
use Illuminate\Support\Facades\Route;

// Home - Driver Standings
Route::get('/', [StandingsController::class, 'index'])->name('home');

// Races
Route::get('/races', [RaceController::class, 'index'])->name('races.index');
Route::get('/races/{race}', [RaceController::class, 'show'])->name('races.show');

// Drivers
Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
Route::get('/drivers/{driver}', [DriverController::class, 'show'])->name('drivers.show');

// Sessions
Route::get('/sessions/{session}/laps', [SessionController::class, 'laps'])->name('sessions.laps');
