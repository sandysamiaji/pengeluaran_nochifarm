<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedProduct extends Model
{
    use HasFactory;

    protected $table = 'feed_products';

    protected $fillable = [
        'name',
        'code',
        'price_per_karung',
        'weight_per_karung',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'price_per_karung' => 'float',
        'weight_per_karung' => 'float',
        'is_active' => 'boolean',
    ];
}
