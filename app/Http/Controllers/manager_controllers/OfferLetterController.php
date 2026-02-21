<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;

class OfferLetterController extends Controller
{
    public function index()
    {
        return view('pages.manager.offerletter.offerLetter');
    }
}