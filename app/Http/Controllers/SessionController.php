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
        $session->load('race.season');

        // Get fastest lap per driver using SQL
        $fastestLaps = $session->laps()
            ->select('laps.*')
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY driver_id ORDER BY lap_duration ASC) as rn')
            ->with('driver.constructor')
            ->where('is_pit_out_lap', false)
            ->whereNotNull('lap_duration')
            ->get()
            ->where('rn', 1)
            ->sortBy('lap_duration')
            ->values();

        return view('sessions.laps', compact('session', 'fastestLaps'));
    }
}
