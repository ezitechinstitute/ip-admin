<?php

namespace App\Services\Invoices;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class InvoicePDFService
{
    public static function generate($invoice, $action = 'download')
    {
        try {
            $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))
                      ->setPaper('a4', 'portrait')
                      ->setOptions([
                          'defaultFont' => 'sans-serif',
                          'isHtml5ParserEnabled' => true,
                          'isRemoteEnabled' => true
                      ]);

            if ($action === 'stream') {
                return $pdf->stream('invoice-' . $invoice->inv_id . '.pdf');
            }

            return $pdf->download('invoice-' . $invoice->inv_id . '.pdf');

        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }
}