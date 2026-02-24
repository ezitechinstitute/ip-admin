<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;

class OfferLetterTemplateController extends Controller
{
    public function index()
    {
        return view('pages.manager.offer-letter-template.offerLetterTemplate');
    }
}