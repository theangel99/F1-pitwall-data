<?php

declare(strict_types=1);

namespace App\DTOs\OpenF1;

use DateTimeInterface;

/**
 * Data Transfer Object for OpenF1 Meeting data.
 */
readonly class MeetingDTO
{
    /**
     * Create a new MeetingDTO instance.
     *
     * @param int $meetingKey OpenF1 meeting key
     * @param string $meetingName Name of the meeting (e.g., British Grand Prix)
     * @param string $meetingOfficialName Official name of the meeting
     * @param string|null $circuitShortName Short name of the circuit
     * @param string|null $location Location of the meeting
     * @param string|null $countryName Name of the country
     * @param int|null $year Year of the meeting
     * @param DateTimeInterface|null $dateStart Meeting start date
     */
    public function __construct(
        public int $meetingKey,
        public string $meetingName,
        public string $meetingOfficialName,
        public ?string $circuitShortName,
        public ?string $location,
        public ?string $countryName,
        public ?int $year,
        public ?DateTimeInterface $dateStart,
    ) {}
}
