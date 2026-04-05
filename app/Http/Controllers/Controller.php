<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function logActivity($action, $details = null)
    {
        $managerId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');

        if (\Illuminate\Support\Facades\Schema::hasTable('supervisor_activity_logs')) {
            \Illuminate\Support\Facades\DB::table('supervisor_activity_logs')->insert([
                'manager_id' => $managerId,        // ✅ FIX ADDED
                'supervisor_id' => $managerId,     // ✅ keep this (your system uses same ID)
                'action' => $action,
                'details' => $details,
                'created_at' => now(),
            ]);
        }
    }

    protected function notifyIntern($eti_id, $type, $message)
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        if (\Illuminate\Support\Facades\Schema::hasTable('supervisor_notifications')) {
            \Illuminate\Support\Facades\DB::table('supervisor_notifications')->insert([
                'supervisor_id' => $supervisorId,
                'type' => $type,
                'eti_id' => $eti_id,
                'message' => $message,
                'is_read' => false,
                'created_at' => now(),
            ]);
        }
    }
}
