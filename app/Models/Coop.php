<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coop extends Model
{
    use HasFactory;

    protected $table = 'coops';

    protected $fillable = [
        'flock_id',
        'name',
        'code',
        'capacity',
        'active_chickens',
        'chicken_age_weeks',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'active_chickens' => 'integer',
        'chicken_age_weeks' => 'integer',
        'is_active' => 'boolean',
    ];

    public function flock()
    {
        return $this->belongsTo(Flock::class);
    }
}
