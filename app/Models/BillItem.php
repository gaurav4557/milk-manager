<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    protected $fillable = [
        'bill_id',
        'entry_date',
        'quantity',
        'rate_per_litre',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'quantity' => 'decimal:3',
            'rate_per_litre' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Every bill item belongs to one bill.
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }
}
