<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'address',
        'is_active',
        'archived',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'archived' => 'boolean',
    ];

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }
}
