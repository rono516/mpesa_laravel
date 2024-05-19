<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'merchant',
        'checkout',
        'receipt',
        'phone',
        'amount',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::substrReplace($value, '0', 0, 3),
        );
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->diffForHumans(),
        );
    }
}
