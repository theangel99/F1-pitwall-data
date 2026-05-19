<?php

declare(strict_types=1);

namespace App\Services\OpenF1;

use App\Enums\SyncType;
use App\Jobs\SyncDriversJob;
use App\Jobs\SyncLapsJob;
use App\Jobs\SyncPositionsJob;
use App\Jobs\SyncSessionsJob;
use App\Models\Race;
use App\Models\Season;
use App\Services\OpenF1\Transformers\MeetingTransformer;
use Illuminate\Support\Facades\Log;

/**
 * Service to orchestrate syncing data from OpenF1 API.
 */
class OpenF1SyncService
{
    /**
     * Create a new OpenF1SyncService instance.
     *
     * @param OpenF1Client $client
     */
    public function __construct(
        private readonly OpenF1Client $client,
    ) {}

    /**
     * Sync data based on the sync type.
     *
     * @param SyncType $type
     * @param array<string, mixed> $params
     * @return void
     */
    public function sync(SyncType $type, array $params = []): void
    {
        Log::info('Starting sync', ['type' => $type->value, 'params' => $params]);

        match ($type) {
            SyncType::MEETINGS => $this->syncMeetings($params['year'] ?? (int) date('Y')),
            SyncType::SESSIONS => $this->syncSessions(),
            SyncType::DRIVERS => $this->syncDrivers($params['session_key'] ?? null),
            SyncType::LAPS => $this->syncLaps($params['session_key'] ?? null),
            SyncType::POSITIONS => $this->syncPositions($params['session_key'] ?? null),
            SyncType::PIT_STOPS => $this->syncPitStops($params['session_key'] ?? null),
        };

        Log::info('Sync completed', ['type' => $type->value]);
    }

    /**
     * Sync meetings for a given year.
     *
     * @param int $year
     * @return void
     */
    private function syncMeetings(int $year): void
    {
        $season = Season::firstOrCreate(['year' => $year]);

        $rawMeetings = $this->client->getMeetings($year);

        foreach ($rawMeetings as $rawMeeting) {
            $meetingDTO = MeetingTransformer::fromArray($rawMeeting);

            Race::updateOrCreate(
                ['openf1_meeting_key' => $meetingDTO->meetingKey],
                [
                    'season_id' => $season->id,
                    'name' => $meetingDTO->meetingName,
                    'circuit' => $meetingDTO->circuitShortName ?? 'Unknown',
                    'country' => $meetingDTO->countryName ?? 'Unknown',
                    'location' => $meetingDTO->location,
                    'date' => $meetingDTO->dateStart ?? now(),
                ],
            );
        }
    }

    /**
     * Sync sessions for all races.
     *
     * @return void
     */
    private function syncSessions(): void
    {
        $races = Race::all();

        foreach ($races as $race) {
            SyncSessionsJob::dispatch($race->openf1_meeting_key);
        }
    }

    /**
     * Sync drivers for a session.
     *
     * @param int|null $sessionKey
     * @return void
     */
    private function syncDrivers(?int $sessionKey): void
    {
        if ($sessionKey) {
            SyncDriversJob::dispatch($sessionKey);
        }
    }

    /**
     * Sync laps for a session.
     *
     * @param int|null $sessionKey
     * @return void
     */
    private function syncLaps(?int $sessionKey): void
    {
        if ($sessionKey) {
            SyncLapsJob::dispatch($sessionKey);
        }
    }

    /**
     * Sync positions for a session.
     *
     * @param int|null $sessionKey
     * @return void
     */
    private function syncPositions(?int $sessionKey): void
    {
        if ($sessionKey) {
            SyncPositionsJob::dispatch($sessionKey);
        }
    }

    /**
     * Sync pit stops for a session.
     *
     * @param int|null $sessionKey
     * @return void
     */
    private function syncPitStops(?int $sessionKey): void
    {
        // TODO(#1): Implement pit stop syncing
        Log::info('Pit stop sync not yet implemented', ['session_key' => $sessionKey]);
    }
}
