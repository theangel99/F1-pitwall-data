<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Types of data that can be synced from OpenF1 API.
 */
enum SyncType: string
{
    case MEETINGS = 'meetings';
    case DRIVERS = 'drivers';
    case SESSIONS = 'sessions';
    case LAPS = 'laps';
    case POSITIONS = 'positions';
    case PIT_STOPS = 'pit_stops';

    /**
     * Get all sync type values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $type) => $type->value, self::cases());
    }

    /**
     * Get a human-readable label for the sync type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::MEETINGS => 'Meetings',
            self::DRIVERS => 'Drivers',
            self::SESSIONS => 'Sessions',
            self::LAPS => 'Laps',
            self::POSITIONS => 'Positions',
            self::PIT_STOPS => 'Pit Stops',
        };
    }
}
