<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseTemplate extends Model
{
    use HasFactory;

    protected $table = 'expense_templates';

    protected $fillable = [
        'name',
        'icon',
        'category',
        'subcategory',
        'purpose',
        'amount',
        'notes',
        'is_active',
        'order_num',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'order_num' => 'integer',
    ];

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
