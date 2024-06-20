<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [
        'updated_at',
        'created_at'
    ];

    const WITHDRAW = 'withdraw';

    const DEPOSIT = 'deposit';

    const TRANSFER = 'transfer';

    public function from()
    {
        return $this->belongsTo(
            User::class,
            'from',
        );
    }

    public function to()
    {
        return $this->belongsTo(
            User::class,
            'to',
        );
    }
}
