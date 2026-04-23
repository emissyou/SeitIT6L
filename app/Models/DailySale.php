<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailySale extends Model
{
    protected $fillable = [
        'employee_id',
        'sales_date',
        'gross_sales',
        'total_discount',
        'total_credit',
        'net_sales',
        'status',
        'opened_at',
        'closed_at',
        'discount_details',
        'credit_details',
        'opening_readings',
        'closing_readings',
        'archived',
    ];

    protected $casts = [
        'sales_date' => 'date',
        'gross_sales' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'net_sales' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'discount_details' => 'array',
        'credit_details' => 'array',
        'opening_readings' => 'array',
        'closing_readings' => 'array',
        'archived' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }
}
