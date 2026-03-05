<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;          // ✅ CORRECT
use App\Models\AdminSetting;
use App\Models\PaymentVoucher;
use Illuminate\Support\Facades\DB;

class RemainingAmountController extends Controller
{
    public function index()
    {
        $RemainingAmount=DB::table('intern_remaining_amounts')->paginate(10);

        return view('pages.manager.remainingBalance.remainingBalance',compact('RemainingAmount'));
}}