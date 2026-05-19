<?php

declare(strict_types=1);

use App\Http\Resources\DriverResource;
use App\Http\Resources\LapResource;
use App\Http\Resources\RaceResource;
use App\Http\Resources\StandingsResource;
use App\Models\Driver;
use App\Models\Race;
use App\Models\Season;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API v1 Routes
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {

    // Drivers
    Route::get('/drivers', function () {
        $drivers = Driver::with('constructor')->get();
        return DriverResource::collection($drivers);
    });

    Route::get('/drivers/{driver}', function (Driver $driver) {
        $driver->load('constructor', 'raceResults.race');
        return new DriverResource($driver);
    });

    // Races
    Route::get('/races', function () {
        $races = Race::with(['season', 'sessions'])->orderBy('date')->get();
        return RaceResource::collection($races);
    });

    Route::get('/races/{race}', function (Race $race) {
        $race->load(['season', 'sessions', 'raceResults.driver.constructor']);
        return new RaceResource($race);
    });

    // Session Laps
    Route::get('/sessions/{session}/laps', function (Session $session) {
        $laps = $session->laps()
            ->with('driver.constructor')
            ->where('is_pit_out_lap', false)
            ->whereNotNull('lap_duration')
            ->orderBy('lap_duration')
            ->limit(100)
            ->get();

        return LapResource::collection($laps);
    });

    // Driver Standings
    Route::get('/standings/drivers', function () {
        $currentSeason = Season::where('year', (int) date('Y'))
            ->with(['races.raceResults.driver.constructor'])
            ->firstOrFail();

        $standings = $currentSeason->races
            ->flatMap(fn ($race) => $race->raceResults)
            ->groupBy('driver_id')
            ->map(function ($results, $index) {
                return [
                    'position' => $index + 1,
                    'driver' => $results->first()->driver,
                    'points' => $results->sum('points'),
                    'wins' => $results->where('position', 1)->count(),
                    'podiums' => $results->whereIn('position', [1, 2, 3])->count(),
                ];
            })
            ->sortByDesc('points')
            ->values();

        return StandingsResource::collection($standings);
    });

    // Constructor Standings
    Route::get('/standings/constructors', function () {
        $currentSeason = Season::where('year', (int) date('Y'))
            ->with(['races.raceResults.driver.constructor'])
            ->firstOrFail();

        $standings = $currentSeason->races
            ->flatMap(fn ($race) => $race->raceResults)
            ->groupBy('driver.constructor_id')
            ->map(function ($results, $index) {
                return [
                    'position' => $index + 1,
                    'constructor' => $results->first()->driver->constructor,
                    'points' => $results->sum('points'),
                    'wins' => $results->where('position', 1)->count(),
                ];
            })
            ->filter(fn ($standing) => $standing['constructor'] !== null)
            ->sortByDesc('points')
            ->values();

        return StandingsResource::collection($standings);
    });
});
