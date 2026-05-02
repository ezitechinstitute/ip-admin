<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Includes fields from Admin, Manager, and Intern tables[cite: 2, 3, 4].
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',                // admin, manager, supervisor, intern[cite: 1, 7]
        'assigned_modules',    // JSON field for module permissions
        'image',               // Profile photo[cite: 2, 3]
        'legacy_manager_id',   // Old ID from manager_accounts[cite: 2]
        'legacy_intern_id',    // Old ID from intern_accounts[cite: 4]
        'eti_id',              // Employee/Intern unique ID
        'department',          // For Managers/Supervisors[cite: 2]
        'int_technology',      // For Interns[cite: 4]
        'portal_status',       // active, frozen, pending[cite: 4]
        'supervisor_id',       // Relationship mapping[cite: 4]
        'manager_id',          // Relationship mapping[cite: 4]
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'assigned_modules' => 'array', // Automatically handles JSON
        ];
    }

    /**
     * Helper to check access for same-named modules.
     * Example: $user->hasModuleAccess('invoices'); 
     * If user is a manager, it checks for 'manager.invoices'.
     */
    public function hasModuleAccess($moduleName)
{
    if (strtolower($this->role) === 'admin') return true;

    $modules = $this->assigned_modules ?? [];

    // Check 1: Exact match (intern.dashboard)
    // Check 2: Namespaced match (intern . dashboard)
    return in_array($moduleName, $modules) || 
           in_array($this->role . '.' . $moduleName, $modules);
}
}