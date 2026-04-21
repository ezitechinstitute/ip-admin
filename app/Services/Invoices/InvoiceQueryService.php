<?php

namespace App\Services\Invoices;

use App\Models\Invoice;

class InvoiceQueryService
{
    public static function filter($request, $query = null)
    {
        $query = $query ?: Invoice::query();

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'paid':
                    $query->where('remaining_amount', '<=', 0);
                    break;
                case 'pending':
                    $query->where('remaining_amount', '>', 0)
                          ->where('due_date', '>=', now());
                    break;
                case 'overdue':
                    $query->where('due_date', '<', now())
                          ->where('remaining_amount', '>', 0);
                    break;
                case 'pending_approval':
                    $query->where('approval_status', 'pending');
                    break;
            }
        }

        // Invoice type filter
        if ($request->filled('invoice_type')) {
            $query->where('invoice_type', $request->invoice_type);
        }

        // Technology filter
        if ($request->filled('technology')) {
            $query->where('technology', $request->technology);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('inv_id', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('intern_email', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }
}