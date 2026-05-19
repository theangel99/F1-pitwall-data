<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\SessionType;
use App\Models\Session;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Session repository operations.
 */
interface SessionRepositoryInterface
{
    /**
     * Get all sessions for a specific race.
     *
     * @param string $raceId
     * @return Collection<int, Session>
     */
    public function getByRace(string $raceId): Collection;

    /**
     * Find a session by its OpenF1 session key.
     *
     * @param int $sessionKey
     * @return Session|null
     */
    public function findBySessionKey(int $sessionKey): ?Session;

    /**
     * Get the latest completed session.
     *
     * @return Session|null
     */
    public function getLatestCompleted(): ?Session;

    /**
     * Get sessions by type for a specific race.
     *
     * @param string $raceId
     * @param SessionType $type
     * @return Collection<int, Session>
     */
    public function getByRaceAndType(string $raceId, SessionType $type): Collection;
}
