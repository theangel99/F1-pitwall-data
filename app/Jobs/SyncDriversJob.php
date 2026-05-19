<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Constructor;
use App\Models\Driver;
use App\Repositories\Contracts\SessionRepositoryInterface;
use App\Services\OpenF1\OpenF1Client;
use App\Services\OpenF1\Transformers\DriverTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to sync driver data from OpenF1 API.
 */
class SyncDriversJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int $sessionKey The OpenF1 session key to sync drivers for
     */
    public function __construct(
        private readonly int $sessionKey,
    ) {}

    /**
     * Execute the job.
     *
     * @param OpenF1Client $client
     * @param SessionRepositoryInterface $sessionRepository
     * @return void
     */
    public function handle(OpenF1Client $client, SessionRepositoryInterface $sessionRepository): void
    {
        Log::info('Starting driver sync', ['session_key' => $this->sessionKey]);

        try {
            $rawDrivers = $client->getDrivers($this->sessionKey);

            foreach ($rawDrivers as $rawDriver) {
                $driverDTO = DriverTransformer::fromArray($rawDriver);

                // Find or create constructor
                $constructor = Constructor::firstOrCreate(
                    ['openf1_team_name' => $driverDTO->teamName],
                    [
                        'name' => $driverDTO->teamName,
                        'color_hex' => ltrim($driverDTO->teamColour, '#'),
                    ],
                );

                // Upsert driver
                Driver::updateOrCreate(
                    ['openf1_driver_id' => $driverDTO->nameAcronym . '_' . $driverDTO->driverNumber],
                    [
                        'constructor_id' => $constructor->id,
                        'openf1_driver_number' => $driverDTO->driverNumber,
                        'code' => $driverDTO->nameAcronym,
                        'full_name' => $driverDTO->fullName,
                        'nationality' => $driverDTO->countryCode,
                    ],
                );
            }

            Log::info('Driver sync completed', [
                'session_key' => $this->sessionKey,
                'count' => $rawDrivers->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Driver sync failed', [
                'session_key' => $this->sessionKey,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
