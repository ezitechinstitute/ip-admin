<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    
    protected $fillable = [
        'invoice_id',
        'inv_id',
        'amount',
        'payment_date',        // Add this
        'type',
        'method',
        'notes',
        'payment_date',
        'created_by',
        'created_by_name',
        'screenshot'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    // Relationship with Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // Get payment method badge color
    public function getMethodBadgeAttribute()
    {
        $colors = [
            'cash' => 'success',
            'bank_transfer' => 'info',
            'credit_card' => 'warning',
            'cheque' => 'secondary'
        ];
        return $colors[$this->method] ?? 'primary';
    }

    // Format amount
    public function getFormattedAmountAttribute()
    {
        return 'PKR ' . number_format($this->amount, 2);
    }
}