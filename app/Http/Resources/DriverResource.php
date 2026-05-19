<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource for Driver model.
 *
 * @mixin \App\Models\Driver
 */
class DriverResource extends JsonResource
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
            'driver_number' => $this->openf1_driver_number,
            'code' => $this->code,
            'full_name' => $this->full_name,
            'nationality' => $this->nationality,
            'headshot_url' => $this->headshot_url,
            'constructor' => [
                'id' => $this->constructor?->id,
                'name' => $this->constructor?->name,
                'color' => $this->constructor?->color_hex,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
