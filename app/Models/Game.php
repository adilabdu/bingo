<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'game_category_id',
        'status',
        'scheduled_at',
        'winner_net_amount',
        'draw_numbers',
        'winning_numbers',
        'winner_player_id'

    ];

    public function gameCategory()
    {
        return $this->belongsTo(GameCategory::class);
    }

    public function players()
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function cartelas()
    {
        return $this->hasManyThrough(Cartela::class, GamePlayer::class);
    }

    protected $casts = [
        'scheduled_at' => 'datetime',
        'draw_numbers' => 'array',
        'winning_numbers' => 'array',
    ];
}
