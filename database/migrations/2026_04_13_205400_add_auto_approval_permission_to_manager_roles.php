<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add invoice_auto_approval permission for all active managers
        // This allows managers to auto-approve invoices without admin review
        $managers = DB::table('manager_accounts')
            ->where('status', 1)
            ->get();

        foreach ($managers as $manager) {
            // Check if permission already exists to avoid duplicates
            $exists = DB::table('manager_roles')
                ->where('manager_id', $manager->manager_id)
                ->where('permission_key', 'invoice_auto_approval')
                ->exists();

            if (!$exists) {
                DB::table('manager_roles')->insert([
                    'manager_id' => $manager->manager_id,
                    'permission_key' => 'invoice_auto_approval',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove auto-approval permission
        DB::table('manager_roles')
            ->where('permission_key', 'invoice_auto_approval')
            ->delete();
    }
};