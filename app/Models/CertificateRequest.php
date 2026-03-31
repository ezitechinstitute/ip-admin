<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateRequest extends Model
{
    protected $fillable = [
        'certificate_request_id',
        'intern_id',
        'intern_name',
        'email',
        'manager_id',
        'certificate_type',
        'status',
        'reason',
        'pdf_path',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];
}
