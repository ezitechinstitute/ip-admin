<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    
    // Specify which timestamp columns exist
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null; // Disable updated_at since it doesn't exist
    
   protected $fillable = [
    'inv_id',
    'screenshot',
    'name',
    'contact',
    'intern_email',
    'technology',
    'total_amount',
    'received_amount',
    'remaining_amount',
    'due_date',
    'received_by',
    'status',
    'approval_status',
    'invoice_type',
    'created_at',
    // REMOVED: intern_id, notes, next_due_date, payment_type
];

    protected $casts = [
        'due_date' => 'date',
        'next_due_date' => 'date',
        'total_amount' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    // Relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'invoice_id');
    }

    public function intern()
    {
        return $this->belongsTo(InternAccount::class, 'intern_email', 'email');
    }

    // Generate Invoice ID
    public static function generateInvoiceId()
    {
        $lastInvoice = self::orderBy('id', 'desc')->first();
        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->inv_id, 4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1001;
        }
        return 'INV-' . $newNumber;
    }

    // Check if overdue
    public function isOverdue()
    {
        return now()->gt($this->due_date) && $this->remaining_amount > 0;
    }

    // Get payment status
    public function getPaymentStatusAttribute()
    {
        if ($this->remaining_amount <= 0) {
            return ['label' => 'Paid', 'class' => 'success'];
        } elseif ($this->due_date < now()) {
            return ['label' => 'Overdue', 'class' => 'danger'];
        } else {
            return ['label' => 'Pending', 'class' => 'warning'];
        }
    }

    // Get approval status
  /**
 * Get approval status
 */
public function getApprovalStatusAttribute($value)
{
    // If the column doesn't exist in DB, return default
    if (!isset($this->attributes['approval_status'])) {
        return 'approved';
    }
    
    $statuses = [
        'pending' => ['label' => 'Pending Approval', 'class' => 'warning'],
        'approved' => ['label' => 'Approved', 'class' => 'success'],
        'rejected' => ['label' => 'Rejected', 'class' => 'danger'],
    ];
    
    return $statuses[$value] ?? ['label' => 'Pending', 'class' => 'warning'];
}
    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('remaining_amount', '>', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('remaining_amount', '<=', 0);
    }

    public function scopePending($query)
    {
        return $query->where('remaining_amount', '>', 0);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'pending');
    }
}