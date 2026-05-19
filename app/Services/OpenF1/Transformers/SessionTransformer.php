<?php

declare(strict_types=1);

namespace App\Services\OpenF1\Transformers;

use App\DTOs\OpenF1\SessionDTO;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Transforms raw OpenF1 API data into SessionDTO instances.
 */
class SessionTransformer
{
    /**
     * Transform a raw OpenF1 session array into a SessionDTO.
     *
     * @param array<string, mixed> $data Raw session data from OpenF1 API
     * @return SessionDTO
     */
    public static function fromArray(array $data): SessionDTO
    {
        return new SessionDTO(
            sessionKey: (int) $data['session_key'],
            meetingKey: (int) $data['meeting_key'],
            sessionName: (string) ($data['session_name'] ?? ''),
            sessionType: (string) ($data['session_type'] ?? ''),
            dateStart: self::parseDate($data['date_start'] ?? null),
            dateEnd: self::parseDate($data['date_end'] ?? null),
            circuitShortName: isset($data['circuit_short_name']) ? (string) $data['circuit_short_name'] : null,
            year: isset($data['year']) ? (int) $data['year'] : null,
            location: isset($data['location']) ? (string) $data['location'] : null,
            countryName: isset($data['country_name']) ? (string) $data['country_name'] : null,
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
