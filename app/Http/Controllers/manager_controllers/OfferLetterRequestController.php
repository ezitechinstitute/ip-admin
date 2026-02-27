<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OfferLetterRequestController extends Controller
{
    public function index(){
        $offerletters=DB::table('offer_letter_requests')->get();
        return view('pages.manager.offer-letter-request.offerLetterRequest',compact('offerletters'));
    }
}
