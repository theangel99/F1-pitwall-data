<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Race;
use App\Repositories\Contracts\RaceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository for Race model operations.
 */
class RaceRepository implements RaceRepositoryInterface
{
    /**
     * Get all races for a specific season.
     *
     * @param string $seasonId
     * @return Collection<int, Race>
     */
    public function getBySeason(string $seasonId): Collection
    {
        return Race::where('season_id', $seasonId)
            ->orderBy('date')
            ->get();
    }

    /**
     * Find a race by its OpenF1 meeting key.
     *
     * @param int $meetingKey
     * @return Race|null
     */
    public function findByMeetingKey(int $meetingKey): ?Race
    {
        return Race::where('openf1_meeting_key', $meetingKey)->first();
    }

    /**
     * Get upcoming races.
     *
     * @param int $limit
     * @return Collection<int, Race>
     */
    public function getUpcoming(int $limit = 5): Collection
    {
        return Race::where('date', '>=', now())
            ->orderBy('date')
            ->limit($limit)
            ->get();
    }

    /**
     * Get past races.
     *
     * @param int $limit
     * @return Collection<int, Race>
     */
    public function getPast(int $limit = 10): Collection
    {
        return Race::where('date', '<', now())
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }
}
