<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function logActivity($action, $details = null)
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        \Illuminate\Support\Facades\DB::table('supervisor_activity_logs')->insert([
            'supervisor_id' => $supervisorId,
            'action' => $action,
            'details' => $details,
            'created_at' => now(),
        ]);
    }

    protected function notifyIntern($eti_id, $type, $message)
    {
        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
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
