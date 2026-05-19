<?php

declare(strict_types=1);

namespace App\Services\OpenF1\Transformers;

use App\DTOs\OpenF1\PositionDTO;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Transforms raw OpenF1 API data into PositionDTO instances.
 */
class PositionTransformer
{
    /**
     * Transform a raw OpenF1 position array into a PositionDTO.
     *
     * @param array<string, mixed> $data Raw position data from OpenF1 API
     * @return PositionDTO
     */
    public static function fromArray(array $data): PositionDTO
    {
        return new PositionDTO(
            sessionKey: (int) $data['session_key'],
            meetingKey: (int) $data['meeting_key'],
            driverNumber: (int) $data['driver_number'],
            position: (int) $data['position'],
            date: self::parseDate($data['date']),
        );
    }

    /**
     * Parse a date string into a DateTimeInterface.
     *
     * @param string $date The date string to parse
     * @return DateTimeInterface
     */
    private static function parseDate(string $date): DateTimeInterface
    {
        $parsed = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $date);

        if ($parsed === false) {
            $parsed = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date);
        }

        return $parsed !== false ? $parsed : new DateTimeImmutable($date);
    }
}
