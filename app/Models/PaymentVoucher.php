<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    protected $table = 'payment_vouchers'; // change if needed

    protected $fillable = [
        'amount',
        'recipient_type',
        'recipient_id',
        'recipient_name',
        'admin_account_no',
        'date',
        'status'
    ];
}
