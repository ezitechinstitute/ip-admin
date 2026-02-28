<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;          // ✅ CORRECT
use App\Models\AdminSetting;
use App\Models\PaymentVoucher;

class PaymentReceiptController extends Controller
{
    public function index(Request $request)
    {
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