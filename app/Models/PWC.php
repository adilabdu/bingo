<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PWC extends Model
{
    use HasFactory;

    protected $table = 'pwc';

    protected $fillable = [
        'game_id',
        'cartela_id',
        'winning_rows'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function cartela(): BelongsTo
    {
        return $this->belongsTo(Cartela::class);
    }
}
