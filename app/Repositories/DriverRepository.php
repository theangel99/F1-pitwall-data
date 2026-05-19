<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Driver;
use App\Repositories\Contracts\DriverRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository for Driver model operations.
 */
class DriverRepository implements DriverRepositoryInterface
{
    /**
     * Get all drivers.
     *
     * @return Collection<int, Driver>
     */
    public function getAll(): Collection
    {
        return Driver::with('constructor')
            ->orderBy('full_name')
            ->get();
    }

    /**
     * Find a driver by their OpenF1 driver ID.
     *
     * @param string $openf1DriverId
     * @return Driver|null
     */
    public function findByOpenF1Id(string $openf1DriverId): ?Driver
    {
        return Driver::where('openf1_driver_id', $openf1DriverId)->first();
    }

    /**
     * Find a driver by their driver number.
     *
     * @param int $driverNumber
     * @return Driver|null
     */
    public function findByDriverNumber(int $driverNumber): ?Driver
    {
        return Driver::where('openf1_driver_number', $driverNumber)->first();
    }

    /**
     * Get drivers for a specific constructor.
     *
     * @param string $constructorId
     * @return Collection<int, Driver>
     */
    public function getByConstructor(string $constructorId): Collection
    {
        return Driver::where('constructor_id', $constructorId)
            ->orderBy('full_name')
            ->get();
    }
}
