<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'accountType',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'accountType' => AccountType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
