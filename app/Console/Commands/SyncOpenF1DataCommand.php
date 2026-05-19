<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SyncType;
use App\Services\OpenF1\OpenF1SyncService;
use Illuminate\Console\Command;

/**
 * Command to sync data from OpenF1 API.
 */
class SyncOpenF1DataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openf1:sync {type? : The type of data to sync (meetings, sessions, laps, etc.)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Formula 1 data from OpenF1 API';

    /**
     * Execute the console command.
     *
     * @param OpenF1SyncService $syncService
     * @return int
     */
    public function handle(OpenF1SyncService $syncService): int
    {
        $type = $this->argument('type');

        if (!$type) {
            $this->info('Syncing all data...');
            $this->syncAll($syncService);
            return self::SUCCESS;
        }

        try {
            $syncType = SyncType::from($type);
            $this->info("Syncing {$syncType->label()}...");
            $syncService->sync($syncType);
            $this->info("Sync completed successfully!");
            return self::SUCCESS;
        } catch (\ValueError $e) {
            $this->error("Invalid sync type: {$type}");
            $this->error("Valid types: " . implode(', ', SyncType::values()));
            return self::FAILURE;
        }
    }

    /**
     * Sync all data types.
     *
     * @param OpenF1SyncService $syncService
     * @return void
     */
    private function syncAll(OpenF1SyncService $syncService): void
    {
        $syncService->sync(SyncType::MEETINGS, ['year' => (int) date('Y')]);
        $this->info('✓ Meetings synced');

        $syncService->sync(SyncType::SESSIONS);
        $this->info('✓ Sessions synced');

        $this->info('All data synced successfully!');
    }
}
