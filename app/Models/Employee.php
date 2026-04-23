<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'contact_number',
        'role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'received_by');
    }

    public function dailySales(): HasMany
    {
        return $this->hasMany(DailySale::class);
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }
}
