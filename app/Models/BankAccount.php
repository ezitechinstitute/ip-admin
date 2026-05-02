<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $table = 'bank_accounts';

    protected $fillable = [
        'account_name',
        'account_number',
        'account_type',
        'account_sub_type',
        'opening_balance',
        'current_balance',
        'note',
        'added_by',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Transactions linked to this bank account
    public function transactions()
    {
        return $this->hasMany(Account::class, 'bank_account_id');
    }

    // Outgoing fund transfers
    public function outgoingTransfers()
    {
        return $this->hasMany(FundTransfer::class, 'from_bank_account_id');
    }

    // Incoming fund transfers
    public function incomingTransfers()
    {
        return $this->hasMany(FundTransfer::class, 'to_bank_account_id');
    }

    // Formatted balance for views
    public function getFormattedBalanceAttribute()
    {
        return 'Rs ' . number_format($this->current_balance, 2);
    }
}