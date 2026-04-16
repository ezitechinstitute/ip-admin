<?php

namespace App\Http\Controllers\cards;

use App\Http\Controllers\Controller;

class CardGamifications extends Controller
{
    public function index()
    {
        // Return a simple view or redirect
        return view('content.cards.card-gamifications');
    }
}