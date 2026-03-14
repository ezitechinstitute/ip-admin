<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceApproval extends Model
{
    protected $table = 'invoice_approvals';
    
    protected $fillable = [
        'invoice_id',
        'inv_id',
        'requested_by',
        'requested_by_name',
        'approved_by',
        'approved_by_name',
        'status',
        'remarks',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}