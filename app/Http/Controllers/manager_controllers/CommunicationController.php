<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Message;
use App\Models\Technology;
use App\Models\InternAccount;
use App\Models\PortalNotification;
use App\Mail\CommunicationMail;
use Illuminate\Support\Facades\Mail;

class CommunicationController extends Controller
{
    //
    public function index()
    {

        $technologies = Technology::where('status',1)->get();

        return view(
            'pages.manager.communication.index',
            compact('technologies')
        );

    }

    public function sendMessage(Request $request)
    {

        $message = Message::create([
            'manager_id'=>auth()->guard('manager')->user()->manager_id,
            'target_type'=>$request->target_type,
            'target_value'=>$request->target_value,
            'title'=>$request->title,
            'message'=>$request->message
        ]);

        $this->notifyUsers($message);

        return back()->with('success','Message sent successfully');

    }



    private function notifyUsers($message)
    {

        if($message->target_type == 'all_interns')
        {
            $interns = InternAccount::all();
        }

        elseif($message->target_type == 'technology')
        {
            $technology = Technology::find($message->target_value);

            $interns = InternAccount::where(
                'int_technology',
                $technology->technology
            )->get();
        }
        elseif($message->target_type == 'supervisor')
        {
            // Supervisor module not implemented yet
            // Will be integrated once supervisor accounts are available
        }

        $interns = $interns->take(2);

        foreach($interns as $intern)
        {

            // Portal notification
            PortalNotification::create([
                'user_id'=>$intern->int_id,
                'title'=>$message->title,
                'message'=>$message->message
            ]);

            // Email notification
           // Mail::to($intern->email)->send(new CommunicationMail($message));
           Mail::to($intern->email)
             ->queue(new CommunicationMail($message));   
                

        }

    }
}
