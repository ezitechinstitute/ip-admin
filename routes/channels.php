<?php

use Illuminate\Support\Facades\Broadcast;

// Add this at the VERY top of the file
Broadcast::routes(['middleware' => ['web', 'auth:admin,manager,intern']]);

Broadcast::channel('chat.{projectId}', function ($user, $projectId) {
    // Check if user is logged in via any guard
    return auth()->guard('admin')->check() || 
           auth()->guard('manager')->check() || 
           auth()->guard('intern')->check();
});