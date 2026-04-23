<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fuel extends Model
{
    protected $fillable = [
        'name',
        'label',
        'price_per_liter',
        'unit',
    ];

    protected $casts = [
        'price_per_liter' => 'decimal:2',
    ];

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function deliveryDetails(): HasMany
    {
        return $this->hasMany(DeliveryDetail::class);
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }
}
