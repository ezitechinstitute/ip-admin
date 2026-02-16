<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Intern;

class University extends Model
{
    protected $table = 'universities';
    protected $primaryKey = 'uni_id';
    public $timestamps = true;

    protected $fillable = [
        'uti',
        'uni_name',
        'uni_email',
        'uni_password',
        'uni_phone',
        'uni_status',
        'account_status',
    ];

  
    public function interns()
    {
        return $this->hasMany(
            Intern::class,
            'university', 
            'uni_name'       
        );
    }
}
