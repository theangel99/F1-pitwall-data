<?php

declare(strict_types=1);

namespace App\Services\OpenF1\Transformers;

use App\DTOs\OpenF1\LapDTO;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Transforms raw OpenF1 API data into LapDTO instances.
 */
class LapTransformer
{
    /**
     * Transform a raw OpenF1 lap array into a LapDTO.
     *
     * @param array<string, mixed> $data Raw lap data from OpenF1 API
     * @return LapDTO
     */
    public static function fromArray(array $data): LapDTO
    {
        return new LapDTO(
            sessionKey: (int) $data['session_key'],
            meetingKey: (int) $data['meeting_key'],
            driverNumber: (int) $data['driver_number'],
            lapNumber: (int) $data['lap_number'],
            lapDuration: isset($data['lap_duration']) ? (float) $data['lap_duration'] : null,
            durationSector1: isset($data['duration_sector_1']) ? (float) $data['duration_sector_1'] : null,
            durationSector2: isset($data['duration_sector_2']) ? (float) $data['duration_sector_2'] : null,
            durationSector3: isset($data['duration_sector_3']) ? (float) $data['duration_sector_3'] : null,
            isPitOutLap: (bool) ($data['is_pit_out_lap'] ?? false),
            dateStart: self::parseDate($data['date_start'] ?? null),
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
