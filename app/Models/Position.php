<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Formula 1 Position model.
 *
 * @property string $id
 * @property string $session_id
 * @property string $driver_id
 * @property int $position
 * @property \Illuminate\Support\Carbon $recorded_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Position extends Model
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
        'position',
        'recorded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
        ];
    }

    /**
     * Get the session this position belongs to.
     *
     * @return BelongsTo<Session, Position>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the driver for this position.
     *
     * @return BelongsTo<Driver, Position>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Scope query to a specific session.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Position> $query
     * @param string $sessionId
     * @return \Illuminate\Database\Eloquent\Builder<Position>
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}
