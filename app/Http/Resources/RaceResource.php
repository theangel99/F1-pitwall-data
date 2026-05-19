<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource for Race model.
 *
 * @mixin \App\Models\Race
 */
class RaceResource extends JsonResource
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
            'name' => $this->name,
            'circuit' => $this->circuit,
            'country' => $this->country,
            'location' => $this->location,
            'date' => $this->date?->toIso8601String(),
            'round_number' => $this->round_number,
            'season' => [
                'id' => $this->season?->id,
                'year' => $this->season?->year,
            ],
            'sessions' => $this->whenLoaded('sessions', function () {
                return $this->sessions->map(fn ($session) => [
                    'id' => $session->id,
                    'type' => $session->type->value,
                    'starts_at' => $session->starts_at?->toIso8601String(),
                ]);
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
