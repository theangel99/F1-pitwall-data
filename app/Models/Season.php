<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Formula 1 Season model.
 *
 * @property string $id
 * @property int $year
 * @property string|null $champion_driver
 * @property string|null $champion_constructor
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Season extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'year',
        'champion_driver',
        'champion_constructor',
    ];

    /**
     * Get all races for this season.
     *
     * @return HasMany<Race>
     */
    public function races(): HasMany
    {
        return $this->hasMany(Race::class);
    }

    /**
     * Get all fantasy teams for this season.
     *
     * @return HasMany<FantasyTeam>
     */
    public function fantasyTeams(): HasMany
    {
        return $this->hasMany(FantasyTeam::class);
    }

    /**
     * Scope query to a specific year.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Season> $query
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Builder<Season>
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }
}
