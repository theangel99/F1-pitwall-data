<?php

declare(strict_types=1);

namespace App\DTOs\OpenF1;

use DateTimeInterface;

/**
 * Data Transfer Object for OpenF1 Lap data.
 */
readonly class LapDTO
{
    /**
     * Create a new LapDTO instance.
     *
     * @param int $sessionKey OpenF1 session key
     * @param int $meetingKey OpenF1 meeting key
     * @param int $driverNumber Driver's racing number
     * @param int $lapNumber Lap number in the session
     * @param float|null $lapDuration Total lap duration in seconds
     * @param float|null $durationSector1 Sector 1 duration in seconds
     * @param float|null $durationSector2 Sector 2 duration in seconds
     * @param float|null $durationSector3 Sector 3 duration in seconds
     * @param bool $isPitOutLap Whether this is a pit out lap
     * @param DateTimeInterface|null $dateStart Lap start timestamp
     */
    public function __construct(
        public int $sessionKey,
        public int $meetingKey,
        public int $driverNumber,
        public int $lapNumber,
        public ?float $lapDuration,
        public ?float $durationSector1,
        public ?float $durationSector2,
        public ?float $durationSector3,
        public bool $isPitOutLap,
        public ?DateTimeInterface $dateStart,
    ) {}
}
