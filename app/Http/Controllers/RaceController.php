<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Race;
use App\Repositories\Contracts\RaceRepositoryInterface;
use Illuminate\Contracts\View\View;

/**
 * Controller for Race-related operations.
 */
class RaceController extends Controller
{
    /**
     * Create a new RaceController instance.
     *
     * @param RaceRepositoryInterface $raceRepository
     */
    public function __construct(
        private readonly RaceRepositoryInterface $raceRepository,
    ) {}

    /**
     * Display a listing of all races.
     *
     * @return View
     */
    public function index(): View
    {
        $upcomingRaces = $this->raceRepository->getUpcoming(limit: 5);
        $pastRaces = $this->raceRepository->getPast(limit: 10);

        return view('races.index', compact('upcomingRaces', 'pastRaces'));
    }

    /**
     * Display the specified race with sessions and results.
     *
     * @param Race $race
     * @return View
     */
    public function show(Race $race): View
    {
        $race->load(['season', 'sessions', 'raceResults.driver.constructor']);

        return view('races.show', compact('race'));
    }
}
