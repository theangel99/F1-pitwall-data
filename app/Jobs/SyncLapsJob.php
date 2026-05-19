<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Lap;
use App\Repositories\Contracts\DriverRepositoryInterface;
use App\Repositories\Contracts\SessionRepositoryInterface;
use App\Services\OpenF1\OpenF1Client;
use App\Services\OpenF1\Transformers\LapTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to sync lap data from OpenF1 API.
 */
class SyncLapsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int $sessionKey The OpenF1 session key to sync laps for
     */
    public function __construct(
        private readonly int $sessionKey,
    ) {}

    /**
     * Execute the job.
     *
     * @param OpenF1Client $client
     * @param SessionRepositoryInterface $sessionRepository
     * @param DriverRepositoryInterface $driverRepository
     * @return void
     */
    public function handle(
        OpenF1Client $client,
        SessionRepositoryInterface $sessionRepository,
        DriverRepositoryInterface $driverRepository,
    ): void {
        Log::info('Starting lap sync', ['session_key' => $this->sessionKey]);

        try {
            $session = $sessionRepository->findBySessionKey($this->sessionKey);

            if (!$session) {
                Log::warning('Session not found', ['session_key' => $this->sessionKey]);
                return;
            }

            $rawLaps = $client->getLaps($this->sessionKey);

            foreach ($rawLaps as $rawLap) {
                $lapDTO = LapTransformer::fromArray($rawLap);

                $driver = $driverRepository->findByDriverNumber($lapDTO->driverNumber);

                if (!$driver) {
                    continue;
                }

                Lap::updateOrCreate(
                    [
                        'session_id' => $session->id,
                        'driver_id' => $driver->id,
                        'lap_number' => $lapDTO->lapNumber,
                    ],
                    [
                        'lap_duration' => $lapDTO->lapDuration,
                        'sector_1' => $lapDTO->durationSector1,
                        'sector_2' => $lapDTO->durationSector2,
                        'sector_3' => $lapDTO->durationSector3,
                        'is_pit_out_lap' => $lapDTO->isPitOutLap,
                    ],
                );
            }

            Log::info('Lap sync completed', [
                'session_key' => $this->sessionKey,
                'count' => $rawLaps->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Lap sync failed', [
                'session_key' => $this->sessionKey,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
