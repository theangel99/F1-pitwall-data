<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Fantasy Pick model.
 *
 * @property string $id
 * @property string $fantasy_team_id
 * @property string $driver_id
 * @property int $points_earned
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class FantasyPick extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fantasy_team_id',
        'driver_id',
        'points_earned',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'points_earned' => 'integer',
        ];
    }

    /**
     * Get the fantasy team this pick belongs to.
     *
     * @return BelongsTo<FantasyTeam, FantasyPick>
     */
    public function fantasyTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class);
    }

    /**
     * Get the driver for this pick.
     *
     * @return BelongsTo<Driver, FantasyPick>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
