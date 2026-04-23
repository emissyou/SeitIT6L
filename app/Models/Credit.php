<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{
    protected $fillable = [
        'customer_id',
        'daily_sale_id',
        'fuel_id',
        'discount_amount',
        'quantity',
        'amount',
        'balance',
        'credit_date',
        'employee_id',
        'status',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'quantity' => 'decimal:3',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'credit_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function dailySale(): BelongsTo
    {
        return $this->belongsTo(DailySale::class);
    }

    public function fuel(): BelongsTo
    {
        return $this->belongsTo(Fuel::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
