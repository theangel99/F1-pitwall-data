<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fantasy Team model.
 *
 * @property string $id
 * @property string $user_id
 * @property string $season_id
 * @property string $name
 * @property int $total_points
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class FantasyTeam extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'season_id',
        'name',
        'total_points',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_points' => 'integer',
        ];
    }

    /**
     * Get the season this fantasy team belongs to.
     *
     * @return BelongsTo<Season, FantasyTeam>
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get all fantasy picks for this team.
     *
     * @return HasMany<FantasyPick>
     */
    public function fantasyPicks(): HasMany
    {
        return $this->hasMany(FantasyPick::class);
    }
}
