<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    use HasFactory;

    protected $table = 'technologies';
    protected $primaryKey = 'tech_id';
    public $timestamps = true;

    protected $fillable = [
        'technology',
        'status',
    ];

    public function curriculums()
    {
        return $this->hasMany(TechnologyCurriculum::class, 'tech_id', 'tech_id');
    }
}
