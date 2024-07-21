<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'winner_player_id',
        'is_tv_game',
        'cashier_id',
        'profit',
    ];

    public function gameCategory(): BelongsTo
    {
        return $this->belongsTo(GameCategory::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function cartelas(): HasManyThrough
    {
        return $this->hasManyThrough(Cartela::class, GamePlayer::class, 'game_id', 'id', 'id', 'cartela_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(Cashier::class);
    }

    public function pwc(): HasOne
    {
        return $this->hasOne(PWC::class, 'game_id');
    }
    protected $casts = [
        'scheduled_at' => 'datetime',
        'draw_numbers' => 'array',
        'winning_numbers' => 'array',
    ];
}
