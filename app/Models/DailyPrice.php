<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPrice extends Model
{
    use HasFactory;

    protected $table = 'daily_prices';

    protected $fillable = [
        'user_id',
        'date',
        'price_peti',
        'price_kg',
    ];

    protected $casts = [
        'date' => 'date',
        'price_peti' => 'float',
        'price_kg' => 'float',
    ];
}
