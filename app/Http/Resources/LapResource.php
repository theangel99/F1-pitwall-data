<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource for Lap model.
 *
 * @mixin \App\Models\Lap
 */
class LapResource extends JsonResource
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
            'id' => $this->id,
            'lap_number' => $this->lap_number,
            'lap_duration' => $this->lap_duration,
            'sector_1' => $this->sector_1,
            'sector_2' => $this->sector_2,
            'sector_3' => $this->sector_3,
            'is_pit_out_lap' => $this->is_pit_out_lap,
            'driver' => [
                'id' => $this->driver?->id,
                'name' => $this->driver?->full_name,
                'code' => $this->driver?->code,
                'number' => $this->driver?->openf1_driver_number,
            ],
            'session' => [
                'id' => $this->session?->id,
                'type' => $this->session?->type->value,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
