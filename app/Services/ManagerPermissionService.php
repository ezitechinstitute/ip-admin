<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManagerPermissionService
{
    public static function has($manager, string $permissionKey): bool
    {
        if (!$manager) {
            return false;
        }

        try {
            return DB::table('manager_roles')
                ->where('manager_id', $manager->manager_id)
                ->where('permission_key', $permissionKey)
                ->exists();
        } catch (\Exception $e) {
            Log::error('Permission check failed: ' . $e->getMessage());
            return false;
        }
    }

    public static function hasAutoApproval($manager): bool
    {
        return self::has($manager, 'invoice_auto_approval');
    }
}