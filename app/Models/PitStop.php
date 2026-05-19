<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Formula 1 Pit Stop model.
 *
 * @property string $id
 * @property string $session_id
 * @property string $driver_id
 * @property int $lap_number
 * @property float|null $duration
 * @property \Illuminate\Support\Carbon|null $pit_in_time
 * @property \Illuminate\Support\Carbon|null $pit_out_time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class PitStop extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_id',
        'driver_id',
        'lap_number',
        'duration',
        'pit_in_time',
        'pit_out_time',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration' => 'float',
            'pit_in_time' => 'datetime',
            'pit_out_time' => 'datetime',
        ];
    }

    /**
     * Get the session this pit stop belongs to.
     *
     * @return BelongsTo<Session, PitStop>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the driver who made this pit stop.
     *
     * @return BelongsTo<Driver, PitStop>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Scope query to a specific session.
     *
     * @param \Illuminate\Database\Eloquent\Builder<PitStop> $query
     * @param string $sessionId
     * @return \Illuminate\Database\Eloquent\Builder<PitStop>
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}
