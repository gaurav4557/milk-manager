<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilkRate extends Model
{
    protected $fillable = [
        'user_id',
        'rate',
        'effective_from',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'effective_from' => 'date',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
