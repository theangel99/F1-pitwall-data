<?php

declare(strict_types=1);

namespace App\Services\OpenF1\Transformers;

use App\DTOs\OpenF1\MeetingDTO;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Transforms raw OpenF1 API data into MeetingDTO instances.
 */
class MeetingTransformer
{
    /**
     * Transform a raw OpenF1 meeting array into a MeetingDTO.
     *
     * @param array<string, mixed> $data Raw meeting data from OpenF1 API
     * @return MeetingDTO
     */
    public static function fromArray(array $data): MeetingDTO
    {
        return new MeetingDTO(
            meetingKey: (int) $data['meeting_key'],
            meetingName: (string) ($data['meeting_name'] ?? ''),
            meetingOfficialName: (string) ($data['meeting_official_name'] ?? ''),
            circuitShortName: isset($data['circuit_short_name']) ? (string) $data['circuit_short_name'] : null,
            location: isset($data['location']) ? (string) $data['location'] : null,
            countryName: isset($data['country_name']) ? (string) $data['country_name'] : null,
            year: isset($data['year']) ? (int) $data['year'] : null,
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
