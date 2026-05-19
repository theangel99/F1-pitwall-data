<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\OpenF1\OpenF1Client;
use App\Services\OpenF1\Transformers\LapTransformer;
use App\Services\OpenF1\Transformers\DriverTransformer;
use App\Models\Lap;
use App\Models\Driver;
use App\Models\Constructor;
use Illuminate\Support\Facades\DB;

echo "==========================================\n";
echo "Syncing 2026 F1 Season Data (Direct)\n";
echo "==========================================\n\n";

$client = app(OpenF1Client::class);

// All session keys for completed races
$sessions = [
    // Australian GP
    11227, 11228, 11229, 11230, 11234,
    // Chinese GP (Sprint)
    11235, 11236, 11240, 11241, 11245,
    // Japanese GP
    11246, 11247, 11248, 11249, 11253,
    // Bahrain GP
    11254, 11255, 11256, 11257, 11261,
    // Saudi Arabian GP
    11262, 11263, 11264, 11265, 11269,
    // Miami GP (Sprint)
    11270, 11271, 11275, 11276, 11280,
];

$total = count($sessions);
$current = 0;
$totalLaps = 0;

foreach ($sessions as $sessionKey) {
    $current++;
    echo "\n[$current/$total] Processing Session $sessionKey...\n";
    echo "----------------------------------------\n";

    try {
        // Find session
        $session = DB::table('f1_sessions')->where('openf1_session_key', $sessionKey)->first();
        if (!$session) {
            echo "  ✗ Session not found\n";
            continue;
        }

        // Sync drivers first
        echo "  → Fetching drivers...\n";
        $rawDrivers = $client->getDrivers($sessionKey);
        echo "    Found " . $rawDrivers->count() . " drivers\n";

        foreach ($rawDrivers as $rawDriver) {
            $driverDTO = DriverTransformer::fromArray($rawDriver);

            // Create/update constructor
            if ($driverDTO->teamName && $driverDTO->teamColor) {
                Constructor::updateOrCreate(
                    ['name' => $driverDTO->teamName],
                    ['color_hex' => $driverDTO->teamColor]
                );
            }

            // Get constructor ID
            $constructor = Constructor::where('name', $driverDTO->teamName)->first();

            // Create/update driver
            Driver::updateOrCreate(
                ['openf1_driver_number' => $driverDTO->driverNumber],
                [
                    'code' => $driverDTO->nameAcronym ?? null,
                    'full_name' => $driverDTO->fullName,
                    'constructor_id' => $constructor?->id,
                ]
            );
        }

        // Sync laps
        echo "  → Fetching laps...\n";
        $rawLaps = $client->getLaps($sessionKey);
        echo "    Found " . $rawLaps->count() . " laps\n";
        echo "    Syncing";

        $lapCount = 0;
        foreach ($rawLaps as $rawLap) {
            $lapDTO = LapTransformer::fromArray($rawLap);
            $driver = Driver::where('openf1_driver_number', $lapDTO->driverNumber)->first();

            if (!$driver) continue;

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
                ]
            );

            $lapCount++;
            if ($lapCount % 100 == 0) {
                echo ".";
            }
        }

        $totalLaps += $lapCount;
        echo "\n  ✓ Session $sessionKey completed ($lapCount laps)\n";

    } catch (Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n==========================================\n";
echo "✓ All data synced successfully!\n";
echo "==========================================\n\n";

echo "Summary:\n";
echo "Drivers: " . Driver::count() . "\n";
echo "Constructors: " . Constructor::count() . "\n";
echo "Total Laps Synced: $totalLaps\n";
echo "Sessions with laps: " . DB::table('f1_sessions')
    ->whereExists(function($q) {
        $q->select(DB::raw(1))
          ->from('laps')
          ->whereColumn('laps.session_id', 'f1_sessions.id');
    })
    ->count() . "\n";
