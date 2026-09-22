<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flock extends Model
{
    use HasFactory;

    protected $table = 'flocks';

    protected $fillable = [
        'name',
        'code',
        'start_date',
        'initial_age_weeks',
        'initial_population',
        'current_population',
        'breed',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_active' => 'boolean',
        'initial_age_weeks' => 'integer',
        'initial_population' => 'integer',
        'current_population' => 'integer',
    ];

    public function coops()
    {
        return $this->hasMany(Coop::class);
    }
}
