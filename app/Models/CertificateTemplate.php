<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'title',
        'content',
        'certificate_type',
        'manager_id',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];
}
