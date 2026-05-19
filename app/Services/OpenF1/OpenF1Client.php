<?php

declare(strict_types=1);

namespace App\Services\OpenF1;

use App\Exceptions\OpenF1ApiException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HTTP client for interacting with the OpenF1 API.
 *
 * Handles all raw HTTP communication with caching and rate limiting.
 */
class OpenF1Client
{
    private const RATE_LIMIT_SLEEP_MICROSECONDS = 350000;

    private string $baseUrl;
    private int $cacheTtl;

    /**
     * Create a new OpenF1Client instance.
     */
    public function __construct()
    {
        $this->baseUrl = config('services.openf1.base_url', 'https://api.openf1.org/v1');
        $this->cacheTtl = (int) config('services.openf1.cache_ttl', 300);
    }

    /**
     * Get all meetings for a specific year.
     *
     * @param int $year The year to fetch meetings for
     * @return Collection<int, array<string, mixed>>
     * @throws OpenF1ApiException
     */
    public function getMeetings(int $year): Collection
    {
        $cacheKey = sprintf('openf1:meetings:%d', $year);

        return Cache::remember(
            key: $cacheKey,
            ttl: $this->cacheTtl,
            callback: fn () => $this->get(endpoint: '/meetings', params: ['year' => $year]),
        );
    }

    /**
     * Get all sessions for a specific meeting.
     *
     * @param int $meetingKey The OpenF1 meeting key
     * @return Collection<int, array<string, mixed>>
     * @throws OpenF1ApiException
     */
    public function getSessions(int $meetingKey): Collection
    {
        $cacheKey = sprintf('openf1:sessions:%d', $meetingKey);

        return Cache::remember(
            key: $cacheKey,
            ttl: $this->cacheTtl,
            callback: fn () => $this->get(endpoint: '/sessions', params: ['meeting_key' => $meetingKey]),
        );
    }

    /**
     * Get all drivers for a specific session.
     *
     * @param int $sessionKey The OpenF1 session key
     * @return Collection<int, array<string, mixed>>
     * @throws OpenF1ApiException
     */
    public function getDrivers(int $sessionKey): Collection
    {
        $cacheKey = sprintf('openf1:drivers:%d', $sessionKey);

        return Cache::remember(
            key: $cacheKey,
            ttl: $this->cacheTtl,
            callback: fn () => $this->get(endpoint: '/drivers', params: ['session_key' => $sessionKey]),
        );
    }

    /**
     * Get lap data for a specific session and optionally a specific driver.
     *
     * @param int $sessionKey The OpenF1 session key
     * @param int|null $driverNumber Optional driver number to filter laps
     * @return Collection<int, array<string, mixed>>
     * @throws OpenF1ApiException
     */
    public function getLaps(int $sessionKey, ?int $driverNumber = null): Collection
    {
        $params = ['session_key' => $sessionKey];

        if ($driverNumber !== null) {
            $params['driver_number'] = $driverNumber;
        }

        $cacheKey = sprintf(
            'openf1:laps:%d:%s',
            $sessionKey,
            $driverNumber ?? 'all',
        );

        return Cache::remember(
            key: $cacheKey,
            ttl: $this->cacheTtl,
            callback: fn () => $this->get(endpoint: '/laps', params: $params),
        );
    }

    /**
     * Get position data for a specific session.
     *
     * @param int $sessionKey The OpenF1 session key
     * @return Collection<int, array<string, mixed>>
     * @throws OpenF1ApiException
     */
    public function getPositions(int $sessionKey): Collection
    {
        $cacheKey = sprintf('openf1:positions:%d', $sessionKey);

        return Cache::remember(
            key: $cacheKey,
            ttl: $this->cacheTtl,
            callback: fn () => $this->get(endpoint: '/position', params: ['session_key' => $sessionKey]),
        );
    }

    /**
     * Get pit stop data for a specific session.
     *
     * @param int $sessionKey The OpenF1 session key
     * @return Collection<int, array<string, mixed>>
     * @throws OpenF1ApiException
     */
    public function getPitStops(int $sessionKey): Collection
    {
        $cacheKey = sprintf('openf1:pitstops:%d', $sessionKey);

        return Cache::remember(
            key: $cacheKey,
            ttl: $this->cacheTtl,
            callback: fn () => $this->get(endpoint: '/pit', params: ['session_key' => $sessionKey]),
        );
    }

    /**
     * Get car data for a specific session and driver.
     *
     * @param int $sessionKey The OpenF1 session key
     * @param int $driverNumber The driver's racing number
     * @return Collection<int, array<string, mixed>>
     * @throws OpenF1ApiException
     */
    public function getCarData(int $sessionKey, int $driverNumber): Collection
    {
        $cacheKey = sprintf('openf1:cardata:%d:%d', $sessionKey, $driverNumber);

        return Cache::remember(
            key: $cacheKey,
            ttl: $this->cacheTtl,
            callback: fn () => $this->get(
                endpoint: '/car_data',
                params: [
                    'session_key' => $sessionKey,
                    'driver_number' => $driverNumber,
                ],
            ),
        );
    }

    /**
     * Make a GET request to the OpenF1 API.
     *
     * @param string $endpoint The API endpoint to call
     * @param array<string, mixed> $params Query parameters
     * @return Collection<int, array<string, mixed>>
     * @throws OpenF1ApiException
     */
    private function get(string $endpoint, array $params = []): Collection
    {
        usleep(self::RATE_LIMIT_SLEEP_MICROSECONDS);

        $url = $this->baseUrl . $endpoint;

        Log::debug('OpenF1 API request', [
            'endpoint' => $endpoint,
            'params' => $params,
        ]);

        $response = $this->client()->get($url, $params);

        if (!$response->successful()) {
            throw OpenF1ApiException::fromResponse($response);
        }

        $data = $response->json();

        if (!is_array($data)) {
            throw new OpenF1ApiException(
                message: 'OpenF1 API returned non-array response',
                code: 500,
            );
        }

        return collect($data);
    }

    /**
     * Create a configured HTTP client instance.
     *
     * @return PendingRequest
     */
    private function client(): PendingRequest
    {
        return Http::timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'PitWall/1.0',
            ]);
    }
}
