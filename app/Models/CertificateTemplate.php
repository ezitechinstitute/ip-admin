<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $table = 'certificate_templates';

    protected $fillable = [
        'type',
        'template_path'
    ];

    public function certificates()
    {
        return $this->hasMany(GeneratedCertificate::class,'template_id');
    }
}