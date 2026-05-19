<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\SessionType;
use App\Models\Session;
use App\Repositories\Contracts\SessionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository for Session model operations.
 */
class SessionRepository implements SessionRepositoryInterface
{
    /**
     * Get all sessions for a specific race.
     *
     * @param string $raceId
     * @return Collection<int, Session>
     */
    public function getByRace(string $raceId): Collection
    {
        return Session::where('race_id', $raceId)
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * Find a session by its OpenF1 session key.
     *
     * @param int $sessionKey
     * @return Session|null
     */
    public function findBySessionKey(int $sessionKey): ?Session
    {
        return Session::where('openf1_session_key', $sessionKey)->first();
    }

    /**
     * Get the latest completed session.
     *
     * @return Session|null
     */
    public function getLatestCompleted(): ?Session
    {
        return Session::where('status', 'completed')
            ->orderBy('starts_at', 'desc')
            ->first();
    }

    /**
     * Get sessions by type for a specific race.
     *
     * @param string $raceId
     * @param SessionType $type
     * @return Collection<int, Session>
     */
    public function getByRaceAndType(string $raceId, SessionType $type): Collection
    {
        return Session::where('race_id', $raceId)
            ->where('type', $type)
            ->orderBy('starts_at')
            ->get();
    }
}
