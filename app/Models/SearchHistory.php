<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_name',
        'weather_data',
        'searched_at',
    ];

    protected $casts = [
        'weather_data' => 'array',
        'searched_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}