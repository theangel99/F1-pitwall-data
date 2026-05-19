<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Formula 1 Race (Meeting) model.
 *
 * @property string $id
 * @property string $season_id
 * @property int $openf1_meeting_key
 * @property string $name
 * @property string $circuit
 * @property string $country
 * @property string|null $location
 * @property \Illuminate\Support\Carbon $date
 * @property int|null $round_number
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Race extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'season_id',
        'openf1_meeting_key',
        'name',
        'circuit',
        'country',
        'location',
        'date',
        'round_number',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    /**
     * Get the season this race belongs to.
     *
     * @return BelongsTo<Season, Race>
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get all sessions for this race.
     *
     * @return HasMany<Session>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Get all race results for this race.
     *
     * @return HasMany<RaceResult>
     */
    public function raceResults(): HasMany
    {
        return $this->hasMany(RaceResult::class);
    }

    /**
     * Scope query to a specific season.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Race> $query
     * @param string $seasonId
     * @return \Illuminate\Database\Eloquent\Builder<Race>
     */
    public function scopeForSeason($query, string $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }
}
