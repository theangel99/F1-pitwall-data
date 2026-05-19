<?php

declare(strict_types=1);

namespace App\DTOs\OpenF1;

use DateTimeInterface;

/**
 * Data Transfer Object for OpenF1 Position data.
 */
readonly class PositionDTO
{
    /**
     * Create a new PositionDTO instance.
     *
     * @param int $sessionKey OpenF1 session key
     * @param int $meetingKey OpenF1 meeting key
     * @param int $driverNumber Driver's racing number
     * @param int $position Current position in the session
     * @param DateTimeInterface $date Timestamp of the position record
     */
    public function __construct(
        public int $sessionKey,
        public int $meetingKey,
        public int $driverNumber,
        public int $position,
        public DateTimeInterface $date,
    ) {}
}
