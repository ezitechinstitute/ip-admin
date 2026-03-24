<?php

namespace App\Models;
use App\Models\InternAccount;
use App\Models\User;
use App\Models\CertificateTemplate;

use Illuminate\Database\Eloquent\Model;

class GeneratedCertificate extends Model
{
    protected $table = 'generated_certificates';

    protected $fillable = [
        'intern_id',
        'template_id',
        'certificate_path',
        'status',
        'approved_by'
    ];
    public function intern()
    {
        return $this->belongsTo(InternAccount::class, 'intern_id', 'int_id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class,'user_id');
    // }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class,'template_id');
    }
}