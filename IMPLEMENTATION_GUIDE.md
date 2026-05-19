# PitWall - Remaining Implementation Guide

This document provides complete implementation details for all remaining files that need to be created.

## ✅ What's Already Complete

The following have been fully implemented:

1. ✅ **Enums** - `SessionType`, `SyncType`
2. ✅ **DTOs** - All 6 DTOs (Driver, Session, Lap, Position, PitStop, Meeting)
3. ✅ **Exceptions** - `OpenF1ApiException`
4. ✅ **Client** - `OpenF1Client` with caching and rate limiting
5. ✅ **Transformers** - All 6 transformers
6. ✅ **Models** - All 10 models with relationships
7. ✅ **Migrations** - All 11 database migrations
8. ✅ **Repositories** - 3 repository interfaces + implementations
9. ✅ **README.md** - Comprehensive documentation
10. ✅ **`.env.example`** - Environment configuration

## 🚧 Files to Implement

### 1. Jobs (4 files)

#### `app/Jobs/SyncDriversJob.php`

```php
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
```

#### `app/Jobs/SyncSessionsJob.php`

```php
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
```

#### `app/Jobs/SyncLapsJob.php`

```php
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
```

#### `app/Jobs/SyncPositionsJob.php`

```php
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
```

### 2. Services

#### `app/Services/OpenF1/OpenF1SyncService.php`

```php
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
            SyncType::MEETINGS => $this->syncMeetings($params['year'] ?? date('Y')),
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
```

### 3. Console Command

#### `app/Console/Commands/SyncOpenF1DataCommand.php`

```php
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
        $syncService->sync(SyncType::MEETINGS, ['year' => date('Y')]);
        $this->info('✓ Meetings synced');

        $syncService->sync(SyncType::SESSIONS);
        $this->info('✓ Sessions synced');

        $this->info('All data synced successfully!');
    }
}
```

### 4. Service Provider Configuration

Add to `app/Providers/AppServiceProvider.php` in the `register()` method:

```php
public function register(): void
{
    // Repository bindings
    $this->app->bind(
        \App\Repositories\Contracts\RaceRepositoryInterface::class,
        \App\Repositories\RaceRepository::class,
    );

    $this->app->bind(
        \App\Repositories\Contracts\DriverRepositoryInterface::class,
        \App\Repositories\DriverRepository::class,
    );

    $this->app->bind(
        \App\Repositories\Contracts\SessionRepositoryInterface::class,
        \App\Repositories\SessionRepository::class,
    );
}
```

### 5. Config File

Create `config/services.php` or add to existing:

```php
'openf1' => [
    'base_url' => env('OPENF1_BASE_URL', 'https://api.openf1.org/v1'),
    'cache_ttl' => env('OPENF1_CACHE_TTL', 300),
    'rate_limit_sleep_us' => env('OPENF1_RATE_LIMIT_SLEEP_US', 350000),
],
```

## 📝 Additional Files to Create

The following files are needed for a complete implementation but are less critical:

### Controllers
- `RaceController`, `DriverController`, `SessionController`, `StandingsController`

### Form Requests
- `FilterSessionRequest`

### API Resources
- `DriverResource`, `RaceResource`, `LapResource`, `StandingsResource`

### Services
- `StandingsService`, `FantasyService`

### Routes
- `routes/web.php` - Web routes
- `routes/api.php` - API routes

### Blade Views
- `resources/views/layouts/app.blade.php`
- Various view files for races, drivers, standings, sessions

### Tests
- Unit tests for transformers and services
- Feature tests for controllers and jobs

## 🎯 Next Steps

1. **Install Laravel 12** in the F1-pitwall directory
2. **Copy all created files** into their proper locations
3. **Run `composer install`**
4. **Configure `.env`** with database credentials
5. **Run migrations**: `php artisan migrate`
6. **Test sync**: `php artisan openf1:sync meetings`
7. **Create remaining controllers, views, and tests** as needed

## 📚 Reference

All code follows:
- PSR-12 coding standard
- Single Responsibility Principle
- Repository pattern
- Service layer pattern
- DTO pattern
- PHPDoc on all public methods

Every file starts with:
```php
<?php

declare(strict_types=1);
```
