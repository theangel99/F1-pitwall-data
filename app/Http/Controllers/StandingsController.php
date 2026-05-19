<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Contracts\View\View;

/**
 * Controller for Championship Standings.
 */
class StandingsController extends Controller
{
    /**
     * Display driver and constructor standings for current season.
     *
     * @return View
     */
    public function index(): View
    {
        $currentSeason = Season::where('year', (int) date('Y'))
            ->with([
                'races' => fn ($query) => $query
                    ->with(['raceResults.driver.constructor'])
                    ->orderBy('date'),
            ])
            ->firstOrFail();

        // Calculate driver standings
        $driverStandings = $currentSeason->races
            ->flatMap(fn ($race) => $race->raceResults)
            ->groupBy('driver_id')
            ->map(function ($results) {
                $driver = $results->first()->driver;
                return [
                    'driver' => $driver,
                    'points' => $results->sum('points'),
                    'wins' => $results->where('position', 1)->count(),
                    'podiums' => $results->whereIn('position', [1, 2, 3])->count(),
                ];
            })
            ->sortByDesc('points')
            ->values();

        // Calculate constructor standings
        $constructorStandings = $currentSeason->races
            ->flatMap(fn ($race) => $race->raceResults)
            ->groupBy('driver.constructor_id')
            ->map(function ($results) {
                $constructor = $results->first()->driver->constructor;
                return [
                    'constructor' => $constructor,
                    'points' => $results->sum('points'),
                    'wins' => $results->where('position', 1)->count(),
                ];
            })
            ->filter(fn ($standing) => $standing['constructor'] !== null)
            ->sortByDesc('points')
            ->values();

        return view('standings.index', compact('currentSeason', 'driverStandings', 'constructorStandings'));
    }
}
