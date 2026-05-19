<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Formula 1 Constructor (Team) model.
 *
 * @property string $id
 * @property string $openf1_team_name
 * @property string $name
 * @property string|null $nationality
 * @property string|null $color_hex
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Constructor extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'openf1_team_name',
        'name',
        'nationality',
        'color_hex',
    ];

    /**
     * Get all drivers for this constructor.
     *
     * @return HasMany<Driver>
     */
    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }
}
