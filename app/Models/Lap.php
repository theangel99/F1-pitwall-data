<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Formula 1 Lap model.
 *
 * @property string $id
 * @property string $session_id
 * @property string $driver_id
 * @property int $lap_number
 * @property float|null $lap_duration
 * @property float|null $sector_1
 * @property float|null $sector_2
 * @property float|null $sector_3
 * @property bool $is_pit_out_lap
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Lap extends Model
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
        'lap_duration',
        'sector_1',
        'sector_2',
        'sector_3',
        'is_pit_out_lap',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lap_duration' => 'float',
            'sector_1' => 'float',
            'sector_2' => 'float',
            'sector_3' => 'float',
            'is_pit_out_lap' => 'boolean',
        ];
    }

    /**
     * Get the session this lap belongs to.
     *
     * @return BelongsTo<Session, Lap>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the driver who completed this lap.
     *
     * @return BelongsTo<Driver, Lap>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Scope query to a specific session.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Lap> $query
     * @param string $sessionId
     * @return \Illuminate\Database\Eloquent\Builder<Lap>
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope query to a specific driver.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Lap> $query
     * @param string $driverId
     * @return \Illuminate\Database\Eloquent\Builder<Lap>
     */
    public function scopeForDriver($query, string $driverId)
    {
        return $query->where('driver_id', $driverId);
    }
}
