<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    use HasFactory;

    protected $table = 'admin_settings';

    protected $fillable = [
        'system_logo',
        'smtp_active_check', // Ye add karein
        'smtp_host',
        'smtp_port',
        'smtp_email',
        'smtp_username',
        'smtp_password',
        'notify_intern_reg',
        'notify_expense',
        'interview_timeout',
        'pagination_limit',
        'expense_categories',
        'internship_duration',
        'export_permissions' 
    ];

    public function getLogoUrlAttribute()
    {
        if ($this->system_logo && file_exists(public_path($this->system_logo))) {
            return asset($this->system_logo);
        }

        return asset('assets/img/branding/logo.png');
    }

   
    protected $casts = [
        'smtp_active_check' => 'boolean',
        'notify_intern_reg' => 'boolean',
        'notify_expense'    => 'boolean',
        'interview_timeout' => 'integer',
        'pagination_limit'  => 'integer',
        'internship_duration' => 'integer',
        'expense_categories' => 'array', // JSON to Array
        'export_permissions' => 'array', // JSON to Array
    ];
}