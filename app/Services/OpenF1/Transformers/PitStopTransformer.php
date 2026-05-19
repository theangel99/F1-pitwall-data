<?php

declare(strict_types=1);

namespace App\Services\OpenF1\Transformers;

use App\DTOs\OpenF1\PitStopDTO;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Transforms raw OpenF1 API data into PitStopDTO instances.
 */
class PitStopTransformer
{
    /**
     * Transform a raw OpenF1 pit stop array into a PitStopDTO.
     *
     * @param array<string, mixed> $data Raw pit stop data from OpenF1 API
     * @return PitStopDTO
     */
    public static function fromArray(array $data): PitStopDTO
    {
        return new PitStopDTO(
            sessionKey: (int) $data['session_key'],
            meetingKey: (int) $data['meeting_key'],
            driverNumber: (int) $data['driver_number'],
            lapNumber: (int) $data['lap_number'],
            pitDuration: isset($data['pit_duration']) ? (float) $data['pit_duration'] : null,
            dateIn: self::parseDate($data['date_in'] ?? null),
            dateOut: self::parseDate($data['date_out'] ?? null),
        );
    }

    /**
     * Parse a date string into a DateTimeInterface.
     *
     * @param string|null $date The date string to parse
     * @return DateTimeInterface|null
     */
    private static function parseDate(?string $date): ?DateTimeInterface
    {
        if ($date === null || $date === '') {
            return null;
        }

        $parsed = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $date);

        if ($parsed === false) {
            $parsed = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date);
        }

        return $parsed !== false ? $parsed : null;
    }
}
