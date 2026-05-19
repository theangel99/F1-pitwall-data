<?php

declare(strict_types=1);

namespace App\DTOs\OpenF1;

/**
 * Data Transfer Object for OpenF1 Driver data.
 */
readonly class DriverDTO
{
    /**
     * Create a new DriverDTO instance.
     *
     * @param int $driverNumber The driver's racing number
     * @param string $broadcastName Name displayed in broadcasts
     * @param string $fullName Driver's full legal name
     * @param string $nameAcronym Three-letter acronym (e.g., VER, HAM)
     * @param string $teamName Name of the driver's team
     * @param string $teamColour Hex color code of the team
     * @param string $countryCode ISO country code of the driver
     * @param int $sessionKey OpenF1 session key
     * @param int $meetingKey OpenF1 meeting key
     */
    public function __construct(
        public int $driverNumber,
        public string $broadcastName,
        public string $fullName,
        public string $nameAcronym,
        public string $teamName,
        public string $teamColour,
        public string $countryCode,
        public int $sessionKey,
        public int $meetingKey,
    ) {}
}
