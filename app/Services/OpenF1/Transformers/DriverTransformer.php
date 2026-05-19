<?php

declare(strict_types=1);

namespace App\Services\OpenF1\Transformers;

use App\DTOs\OpenF1\DriverDTO;

/**
 * Transforms raw OpenF1 API data into DriverDTO instances.
 */
class DriverTransformer
{
    /**
     * Transform a raw OpenF1 driver array into a DriverDTO.
     *
     * @param array<string, mixed> $data Raw driver data from OpenF1 API
     * @return DriverDTO
     */
    public static function fromArray(array $data): DriverDTO
    {
        return new DriverDTO(
            driverNumber: (int) $data['driver_number'],
            broadcastName: (string) ($data['broadcast_name'] ?? ''),
            fullName: (string) ($data['full_name'] ?? ''),
            nameAcronym: (string) ($data['name_acronym'] ?? ''),
            teamName: (string) ($data['team_name'] ?? ''),
            teamColour: (string) ($data['team_colour'] ?? 'FFFFFF'),
            countryCode: (string) ($data['country_code'] ?? ''),
            sessionKey: (int) $data['session_key'],
            meetingKey: (int) $data['meeting_key'],
        );
    }
}
