<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Driver repository operations.
 */
interface DriverRepositoryInterface
{
    /**
     * Get all drivers.
     *
     * @return Collection<int, Driver>
     */
    public function getAll(): Collection;

    /**
     * Find a driver by their OpenF1 driver ID.
     *
     * @param string $openf1DriverId
     * @return Driver|null
     */
    public function findByOpenF1Id(string $openf1DriverId): ?Driver;

    /**
     * Find a driver by their driver number.
     *
     * @param int $driverNumber
     * @return Driver|null
     */
    public function findByDriverNumber(int $driverNumber): ?Driver;

    /**
     * Get drivers for a specific constructor.
     *
     * @param string $constructorId
     * @return Collection<int, Driver>
     */
    public function getByConstructor(string $constructorId): Collection;
}
