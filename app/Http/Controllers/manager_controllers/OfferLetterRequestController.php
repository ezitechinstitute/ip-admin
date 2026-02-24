<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OfferLetterRequestController extends Controller
{
    public function index(){
        return view('pages.manager.offer-letter-request.offerLetterRequest');
    }
}
