<?php

use Illuminate\Support\Facades\Broadcast;

// Laravel 11/12 uses the 'web' guard by default. 
// We must allow any authenticated user from your guards.
Broadcast::channel('chat.{projectId}', function ($user, $projectId) {
    // If you can see this, you are logged in.
    return !is_null($user); 
});
// Broadcast::channel('chat.{projectId}', function ($user, $projectId) {
//     // Check if user is logged in via any guard
//     return auth()->guard('admin')->check() || 
//            auth()->guard('manager')->check() || 
//            auth()->guard('intern')->check();
// });