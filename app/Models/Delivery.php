<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    protected $fillable = [
        'supplier_id',
        'driver',
        'plate_number',
        'received_by',
        'delivery_date',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'total_cost' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DeliveryDetail::class);
    }
}
