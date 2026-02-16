<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    protected $table= 'invoices';

    //   public $timestamps = false;

      protected $fillable = [
            'inv_id',
            'screenshot',
            'name',
            'contact',
            'intern_email',
            'total_amount',
            'received_amount',
            'remaining_amount',
            'due_date', 
            'received_by',
            'status',
            'created_at',
      ];
}
