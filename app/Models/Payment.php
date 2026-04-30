<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    
    // Disable timestamps since migration doesn't have created_at/updated_at
    public $timestamps = false;
    
    protected $fillable = [
        'invoice_id',
        'payment_date',
        'amount_paid'
    ];
    
    /**
     * Get the invoice associated with this payment
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
