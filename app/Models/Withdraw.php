<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\withdraw;

class Withdraw extends Model
{
    protected $table = 'withdraw_requests';

    protected $primaryKey = 'req_id';

    protected $fillable = [
        'eti_id',
        'req_by ',
        'bank',
        'ac_no',
        'ac_name',
        'description',
        'date',
        'amount',
        'req_status',
    ];
}
