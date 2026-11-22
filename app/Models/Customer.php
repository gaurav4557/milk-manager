<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'mobile',
        'address',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function milkEntries(): HasMany
    {
        return $this->hasMany(MilkEntry::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
