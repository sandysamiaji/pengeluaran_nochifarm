<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'date',
        'category',
        'subcategory',
        'purpose',
        'amount',
        'payment_method',
        'notes',
        'receipt_photo',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getReceiptUrlAttribute(): ?string
    {
        if (!$this->receipt_photo) {
            return null;
        }

        if (str_starts_with($this->receipt_photo, 'http')) {
            return $this->receipt_photo;
        }

        return asset($this->receipt_photo);
    }

    /**
     * Scope untuk filter range tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            return $query->where('date', '>=', $startDate);
        } elseif ($endDate) {
            return $query->where('date', '<=', $endDate);
        }

        return $query;
    }
}
