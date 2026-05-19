<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Contracts\View\View;

/**
 * Controller for Session-related operations.
 */
class SessionController extends Controller
{
    /**
     * Display lap times for a specific session.
     *
     * @param Session $session
     * @return View
     */
    public function laps(Session $session): View
    {
        $session->load([
            'race.season',
            'laps' => fn ($query) => $query
                ->with('driver.constructor')
                ->where('is_pit_out_lap', false)
                ->whereNotNull('lap_duration')
                ->orderBy('lap_duration')
                ->limit(100),
        ]);

        // Get fastest lap per driver
        $fastestLaps = $session->laps
            ->groupBy('driver_id')
            ->map(fn ($laps) => $laps->sortBy('lap_duration')->first())
            ->sortBy('lap_duration')
            ->values();

        return view('sessions.laps', compact('session', 'fastestLaps'));
    }
}
