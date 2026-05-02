<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundTransfer extends Model
{
    protected $table = 'fund_transfers';

    protected $fillable = [
        'transfer_id',
        'from_bank_account_id',
        'to_bank_account_id',
        'amount',
        'transfer_date',
        'note',
        'document_path',
        'status',
        'created_by',
        'created_by_role',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transfer_date' => 'date',
    ];

    // Auto-generate TF-00001 style transfer IDs
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            if (empty($transfer->transfer_id)) {
                $last = self::latest('id')->first();
                $nextId = $last ? $last->id + 1 : 1;
                $transfer->transfer_id = 'TF-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Source bank account
    public function fromAccount()
    {
        return $this->belongsTo(BankAccount::class, 'from_bank_account_id');
    }

    // Destination bank account
    public function toAccount()
    {
        return $this->belongsTo(BankAccount::class, 'to_bank_account_id');
    }

    // Formatted amount
    public function getFormattedAmountAttribute()
    {
        return 'Rs ' . number_format($this->amount, 2);
    }

    // Document URL for download
    public function getDocumentUrlAttribute()
    {
        return $this->document_path ? asset('storage/' . $this->document_path) : null;
    }
}