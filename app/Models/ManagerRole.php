<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ManagerAccount;

class ManagerRole extends Model
{
    use HasFactory;

    protected $table = 'manager_roles';

    protected $fillable = [
        'manager_id',
        'permission_key'
    ];

   
    public function manager()
    {
        return $this->belongsTo(ManagerAccount::class, 'manager_id', 'manager_id');
    }
}
