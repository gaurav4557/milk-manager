<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilkEntry extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'entry_date',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'quantity' => 'decimal:3',
        ];
    }

    /**
     * Milk entry belongs to a farmer.
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Milk entry belongs to a customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
