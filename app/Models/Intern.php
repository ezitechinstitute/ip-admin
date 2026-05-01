<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intern extends Model
{
    protected $table = 'intern_table';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'city',
        'phone',
        'cnic',
        'gender',
        'image',
        'join_date',
        'birth_date',
        'university',
        'country',
        'interview_type',
        'technology',
        'duration',
        'status',
        'intern_type',
        'interview_date',
        'interview_time',
        'created_at'

    ];

    /**
 * Get the profile image URL or null.
 */
public function getProfileImageUrlAttribute()
{
    $image = $this->image;
    
    // Skip empty or base64 images
    if (empty($image) || str_starts_with($image, 'data:image')) {
        return null;
    }
    
    // Full URL
    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return $image;
    }
    
    // Public path
    if (file_exists(public_path($image))) {
        return asset($image);
    }
    
    // Storage path
    if (file_exists(storage_path('app/public/' . $image))) {
        return asset('storage/' . $image);
    }
    
    return null;
}

}

