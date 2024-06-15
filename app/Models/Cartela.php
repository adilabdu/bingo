<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cartela extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'numbers'
    ];

    protected $casts = [
        'numbers' => 'array',
    ];

    public function gamePlayers(): HasMany
    {
        return $this->hasMany(GamePlayer::class);
    }
}
