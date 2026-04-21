<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $table = 'withdraw_requests';

    protected $primaryKey = 'req_id';

    protected $fillable = [
        'eti_id',
        'req_by',
        'bank',
        'ac_no',
        'ac_name',
        'description',
        'date',
        'amount',
        'req_status',
        'period',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    // Relationship with ManagersAccount
    public function manager()
    {
        return $this->belongsTo(ManagersAccount::class, 'req_by', 'manager_id');
    }
}
