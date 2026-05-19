<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SessionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Formula 1 Session model.
 *
 * @property string $id
 * @property string $race_id
 * @property int $openf1_session_key
 * @property SessionType $type
 * @property \Illuminate\Support\Carbon $starts_at
 * @property string|null $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Session extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'f1_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'race_id',
        'openf1_session_key',
        'type',
        'starts_at',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => SessionType::class,
            'starts_at' => 'datetime',
        ];
    }

    /**
     * Get the race this session belongs to.
     *
     * @return BelongsTo<Race, Session>
     */
    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    /**
     * Get all laps for this session.
     *
     * @return HasMany<Lap>
     */
    public function laps(): HasMany
    {
        return $this->hasMany(Lap::class);
    }

    /**
     * Get all positions for this session.
     *
     * @return HasMany<Position>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get all pit stops for this session.
     *
     * @return HasMany<PitStop>
     */
    public function pitStops(): HasMany
    {
        return $this->hasMany(PitStop::class);
    }

    /**
     * Scope query to a specific session type.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Session> $query
     * @param SessionType $type
     * @return \Illuminate\Database\Eloquent\Builder<Session>
     */
    public function scopeOfType($query, SessionType $type)
    {
        return $query->where('type', $type);
    }
}
