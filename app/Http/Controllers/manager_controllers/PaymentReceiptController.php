<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\PaymentVoucher;
use Illuminate\Http\Request;          // ✅ CORRECT
use Illuminate\Support\Facades\Auth;

class PaymentReceiptController extends Controller
{
    public function index(Request $request)
    {
        // Check if manager is logged in
    $manager = Auth::guard('manager')->user();

    if (!$manager) {
        return redirect()->route('manager.login');
    }

    // --- Privilege Check ---
    // 'payment_receipt_view' ki jagah apni actual permission key use karein
    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_manager_payment_receipt')) {
        return redirect()->route('manager.dashboard')
                         ->withErrors(['access_denied' => 'You do not have permission to access Payment Receipts.']);
    }
        $pageLimitSet = AdminSetting::first();
        $defaultLimit = $pageLimitSet->pagination_limit ?? 15;

        $perPage = $request->input('per_page', $defaultLimit);

        $Vouchers = PaymentVoucher::orderBy('id', 'desc')
                        ->paginate($perPage)
                        ->withQueryString();

        return view(
            'pages.manager.payment receipt.payment-receipt',
            compact('Vouchers', 'perPage')
        );
    }
}