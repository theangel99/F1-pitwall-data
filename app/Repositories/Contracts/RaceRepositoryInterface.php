<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Race;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Race repository operations.
 */
interface RaceRepositoryInterface
{
    /**
     * Get all races for a specific season.
     *
     * @param string $seasonId
     * @return Collection<int, Race>
     */
    public function getBySeason(string $seasonId): Collection;

    /**
     * Find a race by its OpenF1 meeting key.
     *
     * @param int $meetingKey
     * @return Race|null
     */
    public function findByMeetingKey(int $meetingKey): ?Race;

    /**
     * Get upcoming races.
     *
     * @param int $limit
     * @return Collection<int, Race>
     */
    public function getUpcoming(int $limit = 5): Collection;

    /**
     * Get past races.
     *
     * @param int $limit
     * @return Collection<int, Race>
     */
    public function getPast(int $limit = 10): Collection;
}
