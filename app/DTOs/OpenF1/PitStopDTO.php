<?php

declare(strict_types=1);

namespace App\DTOs\OpenF1;

use DateTimeInterface;

/**
 * Data Transfer Object for OpenF1 Pit Stop data.
 */
readonly class PitStopDTO
{
    /**
     * Create a new PitStopDTO instance.
     *
     * @param int $sessionKey OpenF1 session key
     * @param int $meetingKey OpenF1 meeting key
     * @param int $driverNumber Driver's racing number
     * @param int $lapNumber Lap number when pit stop occurred
     * @param float|null $pitDuration Duration of the pit stop in seconds
     * @param DateTimeInterface|null $dateIn Timestamp when driver entered the pit
     * @param DateTimeInterface|null $dateOut Timestamp when driver exited the pit
     */
    public function __construct(
        public int $sessionKey,
        public int $meetingKey,
        public int $driverNumber,
        public int $lapNumber,
        public ?float $pitDuration,
        public ?DateTimeInterface $dateIn,
        public ?DateTimeInterface $dateOut,
    ) {}
}
