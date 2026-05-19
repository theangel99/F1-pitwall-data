<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Formula 1 Driver model.
 *
 * @property string $id
 * @property string|null $constructor_id
 * @property int $openf1_driver_number
 * @property string $openf1_driver_id
 * @property string $code
 * @property string $full_name
 * @property string|null $nationality
 * @property string|null $headshot_url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Driver extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'constructor_id',
        'openf1_driver_number',
        'openf1_driver_id',
        'code',
        'full_name',
        'nationality',
        'headshot_url',
    ];

    /**
     * Get the constructor this driver drives for.
     *
     * @return BelongsTo<Constructor, Driver>
     */
    public function constructor(): BelongsTo
    {
        return $this->belongsTo(Constructor::class);
    }

    /**
     * Get all laps for this driver.
     *
     * @return HasMany<Lap>
     */
    public function laps(): HasMany
    {
        return $this->hasMany(Lap::class);
    }

    /**
     * Get all positions for this driver.
     *
     * @return HasMany<Position>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get all pit stops for this driver.
     *
     * @return HasMany<PitStop>
     */
    public function pitStops(): HasMany
    {
        return $this->hasMany(PitStop::class);
    }

    /**
     * Get all race results for this driver.
     *
     * @return HasMany<RaceResult>
     */
    public function raceResults(): HasMany
    {
        return $this->hasMany(RaceResult::class);
    }

    /**
     * Get all fantasy picks for this driver.
     *
     * @return HasMany<FantasyPick>
     */
    public function fantasyPicks(): HasMany
    {
        return $this->hasMany(FantasyPick::class);
    }
}
