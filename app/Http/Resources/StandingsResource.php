<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource for Championship Standings.
 */
class StandingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'position' => $this->resource['position'] ?? null,
            'driver' => $this->when(isset($this->resource['driver']), function () {
                $driver = $this->resource['driver'];
                return [
                    'id' => $driver->id,
                    'name' => $driver->full_name,
                    'code' => $driver->code,
                    'number' => $driver->openf1_driver_number,
                    'constructor' => [
                        'name' => $driver->constructor?->name,
                        'color' => $driver->constructor?->color_hex,
                    ],
                ];
            }),
            'constructor' => $this->when(isset($this->resource['constructor']), function () {
                $constructor = $this->resource['constructor'];
                return [
                    'id' => $constructor->id,
                    'name' => $constructor->name,
                    'color' => $constructor->color_hex,
                ];
            }),
            'points' => $this->resource['points'] ?? 0,
            'wins' => $this->resource['wins'] ?? 0,
            'podiums' => $this->resource['podiums'] ?? 0,
        ];
    }
}
