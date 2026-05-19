<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Position;
use App\Repositories\Contracts\DriverRepositoryInterface;
use App\Repositories\Contracts\SessionRepositoryInterface;
use App\Services\OpenF1\OpenF1Client;
use App\Services\OpenF1\Transformers\PositionTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to sync position data from OpenF1 API.
 */
class SyncPositionsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int $sessionKey The OpenF1 session key to sync positions for
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
        Log::info('Starting position sync', ['session_key' => $this->sessionKey]);

        try {
            $session = $sessionRepository->findBySessionKey($this->sessionKey);

            if (!$session) {
                Log::warning('Session not found', ['session_key' => $this->sessionKey]);
                return;
            }

            $rawPositions = $client->getPositions($this->sessionKey);

            foreach ($rawPositions as $rawPosition) {
                $positionDTO = PositionTransformer::fromArray($rawPosition);

                $driver = $driverRepository->findByDriverNumber($positionDTO->driverNumber);

                if (!$driver) {
                    continue;
                }

                Position::create([
                    'session_id' => $session->id,
                    'driver_id' => $driver->id,
                    'position' => $positionDTO->position,
                    'recorded_at' => $positionDTO->date,
                ]);
            }

            Log::info('Position sync completed', [
                'session_key' => $this->sessionKey,
                'count' => $rawPositions->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Position sync failed', [
                'session_key' => $this->sessionKey,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
