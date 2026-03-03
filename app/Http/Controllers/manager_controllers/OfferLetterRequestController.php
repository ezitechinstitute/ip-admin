<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;




class OfferLetterRequestController extends Controller
{
    public function index(Request $request)
    {
        $perpage = $request->get('perpage', 15);

        $query = DB::table('offer_letter_requests');

        // 🔎 Search logic
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('offer_letter_id', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('ezi_id', 'like', "%$search%");
            });
        }

        $offerletters = $query
            ->paginate($perpage)
            ->withQueryString();

        return view(
            'pages.manager.offer-letter-request.offerLetterRequest',
            compact('offerletters', 'perpage')
        );
    }
}
