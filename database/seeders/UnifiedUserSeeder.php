<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UnifiedUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Move Admins[cite: 3]
        foreach (DB::table('admin_accounts')->get() as $admin) {
            User::updateOrCreate(['email' => $admin->email], [
                'name' => $admin->name,
                'password' => $admin->password,
                'role' => 'admin',
                'image' => $admin->image,
                'legacy_admin_id' => $admin->id ?? null,
            ]);
        }

        // 2. Move Managers & Supervisors[cite: 2]
        foreach (DB::table('manager_accounts')->get() as $m) {
            User::updateOrCreate(['email' => $m->email], [
                'name' => $m->name,
                'password' => $m->password,
                'role' => strtolower($m->loginas), // 'manager' or 'supervisor'[cite: 7, 8]
                'legacy_manager_id' => $m->manager_id,
                'eti_id' => $m->eti_id,
                'department' => $m->department,
                'image' => $m->image,
            ]);
        }

        // 3. Move Interns[cite: 4]
        foreach (DB::table('intern_accounts')->get() as $i) {
            User::updateOrCreate(['email' => $i->email], [
                'name' => $i->name,
                'password' => $i->password,
                'role' => 'intern',
                'legacy_intern_id' => $i->int_id,
                'eti_id' => $i->eti_id,
                'int_technology' => $i->int_technology,
                'portal_status' => $i->portal_status,
                'supervisor_id' => $i->supervisor_id,
                'manager_id' => $i->manager_id,
                'image' => $i->image, // Change $i->profile_photo to $i->image
            ]);
        }
    }
}