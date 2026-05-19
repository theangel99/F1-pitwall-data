<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Repositories\Contracts\DriverRepositoryInterface;
use Illuminate\Contracts\View\View;

/**
 * Controller for Driver-related operations.
 */
class DriverController extends Controller
{
    /**
     * Create a new DriverController instance.
     *
     * @param DriverRepositoryInterface $driverRepository
     */
    public function __construct(
        private readonly DriverRepositoryInterface $driverRepository,
    ) {}

    /**
     * Display a listing of all drivers.
     *
     * @return View
     */
    public function index(): View
    {
        $drivers = $this->driverRepository->getAll();

        return view('drivers.index', compact('drivers'));
    }

    /**
     * Display the specified driver with statistics.
     *
     * @param Driver $driver
     * @return View
     */
    public function show(Driver $driver): View
    {
        $driver->load([
            'constructor',
            'laps' => fn ($query) => $query->latest()->limit(100),
            'raceResults' => fn ($query) => $query->with('race')->latest()->limit(10),
        ]);

        return view('drivers.show', compact('driver'));
    }
}
