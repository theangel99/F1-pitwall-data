<?php

declare(strict_types=1);

namespace App\DTOs\OpenF1;

use DateTimeInterface;

/**
 * Data Transfer Object for OpenF1 Session data.
 */
readonly class SessionDTO
{
    /**
     * Create a new SessionDTO instance.
     *
     * @param int $sessionKey OpenF1 session key
     * @param int $meetingKey OpenF1 meeting key
     * @param string $sessionName Name of the session (e.g., Practice 1, Race)
     * @param string $sessionType Type of session
     * @param DateTimeInterface $dateStart Session start date and time
     * @param DateTimeInterface|null $dateEnd Session end date and time
     * @param string|null $circuitShortName Short name of the circuit
     * @param int|null $year Year of the session
     * @param string|null $location Location of the session
     * @param string|null $countryName Name of the country
     */
    public function __construct(
        public int $sessionKey,
        public int $meetingKey,
        public string $sessionName,
        public string $sessionType,
        public DateTimeInterface $dateStart,
        public ?DateTimeInterface $dateEnd,
        public ?string $circuitShortName,
        public ?int $year,
        public ?string $location,
        public ?string $countryName,
    ) {}
}
