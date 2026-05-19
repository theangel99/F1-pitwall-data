<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SessionType;
use App\Models\Race;
use App\Models\Session;
use App\Repositories\Contracts\RaceRepositoryInterface;
use App\Services\OpenF1\OpenF1Client;
use App\Services\OpenF1\Transformers\SessionTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to sync session data from OpenF1 API.
 */
class SyncSessionsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int $meetingKey The OpenF1 meeting key to sync sessions for
     */
    public function __construct(
        private readonly int $meetingKey,
    ) {}

    /**
     * Execute the job.
     *
     * @param OpenF1Client $client
     * @param RaceRepositoryInterface $raceRepository
     * @return void
     */
    public function handle(OpenF1Client $client, RaceRepositoryInterface $raceRepository): void
    {
        Log::info('Starting session sync', ['meeting_key' => $this->meetingKey]);

        try {
            $race = $raceRepository->findByMeetingKey($this->meetingKey);

            if (!$race) {
                Log::warning('Race not found for meeting key', ['meeting_key' => $this->meetingKey]);
                return;
            }

            $rawSessions = $client->getSessions($this->meetingKey);

            foreach ($rawSessions as $rawSession) {
                $sessionDTO = SessionTransformer::fromArray($rawSession);

                $sessionType = $this->mapSessionType($sessionDTO->sessionName);

                Session::updateOrCreate(
                    ['openf1_session_key' => $sessionDTO->sessionKey],
                    [
                        'race_id' => $race->id,
                        'type' => $sessionType->value,
                        'starts_at' => $sessionDTO->dateStart,
                        'status' => null,
                    ],
                );
            }

            Log::info('Session sync completed', [
                'meeting_key' => $this->meetingKey,
                'count' => $rawSessions->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Session sync failed', [
                'meeting_key' => $this->meetingKey,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Map session name to SessionType enum.
     *
     * @param string $sessionName
     * @return SessionType
     */
    private function mapSessionType(string $sessionName): SessionType
    {
        return match (true) {
            str_contains(strtolower($sessionName), 'practice 1') => SessionType::PRACTICE_1,
            str_contains(strtolower($sessionName), 'practice 2') => SessionType::PRACTICE_2,
            str_contains(strtolower($sessionName), 'practice 3') => SessionType::PRACTICE_3,
            str_contains(strtolower($sessionName), 'qualifying') => SessionType::QUALIFYING,
            str_contains(strtolower($sessionName), 'sprint') => SessionType::SPRINT,
            str_contains(strtolower($sessionName), 'race') => SessionType::RACE,
            default => SessionType::RACE,
        };
    }
}
