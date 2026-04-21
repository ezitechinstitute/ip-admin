<?php

namespace App\Services\Invoices;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceIdGenerator
{
    /**
     * Generate unique invoice ID using existing invoices table
     * NO new table needed - uses auto-increment ID
     */
    public static function generate(): string
    {
        // Simple and safe - uses existing invoices table
        $nextId = Invoice::max('id') + 1;
        return 'INV-' . $nextId;
    }

    /**
     * Alternative with transaction lock (for high concurrency)
     */
    public static function generateWithLock(): string
    {
        return DB::transaction(function () {
            $lastId = DB::table('invoices')->lockForUpdate()->max('id');
            $nextId = $lastId + 1;
            return 'INV-' . $nextId;
        });
    }

    /**
     * Fallback method (backward compatible)
     */
    public static function generateSimple(): string
    {
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $lastNumber = $lastInvoice ? intval(substr($lastInvoice->inv_id, 4)) : 1000;
        return 'INV-' . ($lastNumber + 1);
    }
}