<?php

namespace App\Services\Invoices;

use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoicePaymentService
{
    public static function record($invoice, $request, $manager)
    {
        DB::beginTransaction();

        try {
            $newReceived = $invoice->received_amount + $request->payment_amount;
            $newRemaining = $invoice->remaining_amount - $request->payment_amount;

            $invoice->received_amount = $newReceived;
            $invoice->remaining_amount = $newRemaining;

            if ($newRemaining <= 0) {
                $invoice->status = 'paid';
            }

            $invoice->save();

            Transaction::create([
                'invoice_id' => $invoice->id,
                'inv_id' => $invoice->inv_id,
                'amount' => $request->payment_amount,
                'type' => 'payment',
                'method' => $request->payment_method,
                'notes' => $request->notes,
                'payment_date' => $request->payment_date,
                'created_by' => $manager->manager_id ?? 0,
                'created_by_name' => $manager->name ?? 'Manager',
            ]);

            DB::commit();

            return ['success' => true, 'message' => 'Payment recorded successfully'];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}