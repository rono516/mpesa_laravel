<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'phone',
        'amount',
        'merchant',
        'checkout',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
