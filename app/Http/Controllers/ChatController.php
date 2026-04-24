<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectChat;
use App\Models\ChatMessage;
use App\Models\InternProject;
use App\Events\MessageSent; // Ensure this event exists and implements ShouldBroadcast
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Initial landing: Redirect to the first available project chat
     */
    public function index()
    {
        $projectId = null;

        // Admin or Global Manager (God Mode)
        if (Auth::guard('admin')->check() || (Auth::guard('manager')->check() && Auth::guard('manager')->id() == 5)) {
            $first = InternProject::first();
            $projectId = $first ? $first->project_id : null;
        } 
        // Standard Supervisor
        elseif (Auth::guard('manager')->check()) {
            $mId = Auth::guard('manager')->id();
            $first = InternProject::where('assigned_by', $mId)->first();
            $projectId = $first ? $first->project_id : null;
        }
        // Intern (Umair)
        elseif (Auth::guard('intern')->check()) {
            $user = Auth::guard('intern')->user();
            $first = InternProject::where('eti_id', $user->eti_id)->first();
            $projectId = $first ? $first->project_id : null;
        }

        if ($projectId) {
            return redirect()->route('chat.show', $projectId);
        }

        return view('content.chat.room', [
            'projects' => collect(),
            'project' => null,
            'messages' => []
        ]);
    }

    /**
     * Display the specific project chat room
     */
    public function show($projectId)
    {
        $project = InternProject::findOrFail($projectId);
        $projects = collect();

        // 1. ADMIN & GLOBAL MANAGER (God Mode)
        if (Auth::guard('admin')->check() || (Auth::guard('manager')->check() && Auth::guard('manager')->id() == 5)) {
            $projects = InternProject::all();
        } 
        // 2. STANDARD SUPERVISORS
        elseif (Auth::guard('manager')->check()) {
            $mId = Auth::guard('manager')->id();
            $projects = InternProject::where('assigned_by', $mId)->get();

            if ((int)$project->assigned_by !== (int)$mId) {
                abort(403, "Unauthorized: You did not assign this project.");
            }
        } 
        // 3. INTERNS
        elseif (Auth::guard('intern')->check()) {
            $user = Auth::guard('intern')->user();
            $projects = InternProject::where('eti_id', $user->eti_id)->get();
            
            if ($project->eti_id !== $user->eti_id) {
                abort(403, "Unauthorized: This project is not assigned to you.");
            }
        }

        $chat = ProjectChat::firstOrCreate(['project_id' => $projectId]);
        $messages = $chat->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        return view('content.chat.room', compact('projects', 'project', 'chat', 'messages'));
    }

    /**
     * AJAX/Real-time Message Sending
     */
    public function sendMessage(Request $request, $projectId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $chat = ProjectChat::firstOrCreate(['project_id' => $projectId]);

        // Define Sender Identity
        $senderType = null;
        $senderId = null;

        if (Auth::guard('admin')->check() || session()->has('admin_id')) {
            $senderType = \App\Models\AdminAccount::class;
            $senderId = Auth::guard('admin')->id() ?? session('admin_id');
        } elseif (Auth::guard('manager')->check() || session()->has('manager_id')) {
            $senderType = \App\Models\ManagersAccount::class;
            $senderId = Auth::guard('manager')->id() ?? session('manager_id');
        } elseif (Auth::guard('intern')->check() || session()->has('int_id')) {
            $senderType = \App\Models\InternAccount::class;
            $senderId = Auth::guard('intern')->id() ?? session('int_id');
        }

        // 1. Save to Database
        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message' => $request->input('message'),
        ]);

        // 2. BROADCAST TO PUSHER
        // This line triggers the real-time event
        broadcast(new MessageSent($message, $projectId))->toOthers();

        // 3. RETURN JSON RESPONSE (For AJAX)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $message->load('sender')
            ]);
        }

        return back();
    }
}