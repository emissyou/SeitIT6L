<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'credit_id',
        'payment_date',
        'amount_paid',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }
}
