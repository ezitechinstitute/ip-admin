<?php

namespace App\Services\Invoices;

use App\Models\Invoice;

class InvoiceStatsService
{
    public static function get()
    {
        return [
            'total' => Invoice::count(),
            'paid' => Invoice::where('remaining_amount', '<=', 0)->count(),
            'pending' => Invoice::where('remaining_amount', '>', 0)
                                ->where('due_date', '>=', now())
                                ->count(),
            'overdue' => Invoice::where('due_date', '<', now())
                                ->where('remaining_amount', '>', 0)
                                ->count(),
     'pending_approval' => Invoice::where('approval_status', 'pending')
    ->where('created_by_role', 'manager')  // ✅ Only manager invoices
    ->count(),
            'total_amount' => Invoice::sum('total_amount'),
            'received_amount' => Invoice::sum('received_amount'),
            'remaining_amount' => Invoice::sum('remaining_amount'),
        ];
    }

    public static function getForIntern($internEmail)
    {
        return [
            'total' => Invoice::where('intern_email', $internEmail)->count(),
            'paid' => Invoice::where('intern_email', $internEmail)
                            ->where('remaining_amount', '<=', 0)->count(),
            'pending' => Invoice::where('intern_email', $internEmail)
                                ->where('remaining_amount', '>', 0)->count(),
        ];
    }
}