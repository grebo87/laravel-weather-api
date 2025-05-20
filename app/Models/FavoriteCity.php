<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteCity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_name',
    ];

    /**
     * Get the user that owns the favorite city.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}