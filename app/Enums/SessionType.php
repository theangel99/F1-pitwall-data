<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Session types available in Formula 1 race weekends.
 */
enum SessionType: string
{
    case PRACTICE_1 = 'practice_1';
    case PRACTICE_2 = 'practice_2';
    case PRACTICE_3 = 'practice_3';
    case QUALIFYING = 'qualifying';
    case SPRINT = 'sprint';
    case RACE = 'race';

    /**
     * Get all session type values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $type) => $type->value, self::cases());
    }

    /**
     * Get a human-readable label for the session type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PRACTICE_1 => 'Practice 1',
            self::PRACTICE_2 => 'Practice 2',
            self::PRACTICE_3 => 'Practice 3',
            self::QUALIFYING => 'Qualifying',
            self::SPRINT => 'Sprint',
            self::RACE => 'Race',
        };
    }
}
