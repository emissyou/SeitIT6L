<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'fuel_id',
        'current_stock',
        'capacity',
    ];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'capacity' => 'decimal:3',
    ];

    public function fuel(): BelongsTo
    {
        return $this->belongsTo(Fuel::class);
    }
}
