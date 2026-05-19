<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Formula 1 Race Result model.
 *
 * @property string $id
 * @property string $race_id
 * @property string $driver_id
 * @property int|null $position
 * @property int $points
 * @property string|null $status
 * @property float|null $fastest_lap
 * @property bool $fastest_lap_point
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class RaceResult extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'race_id',
        'driver_id',
        'position',
        'points',
        'status',
        'fastest_lap',
        'fastest_lap_point',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fastest_lap' => 'float',
            'fastest_lap_point' => 'boolean',
        ];
    }

    /**
     * Get the race for this result.
     *
     * @return BelongsTo<Race, RaceResult>
     */
    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    /**
     * Get the driver for this result.
     *
     * @return BelongsTo<Driver, RaceResult>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Scope query to a specific race.
     *
     * @param \Illuminate\Database\Eloquent\Builder<RaceResult> $query
     * @param string $raceId
     * @return \Illuminate\Database\Eloquent\Builder<RaceResult>
     */
    public function scopeForRace($query, string $raceId)
    {
        return $query->where('race_id', $raceId);
    }
}
