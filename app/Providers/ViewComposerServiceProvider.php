<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share user data with all views
        View::composer('*', function ($view) {
            $user = null;
            $userRole = 'Guest';
            $userImage = asset('assets/img/branding/ezitech.png');
            
            // Check Admin
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $userRole = 'Admin';
                if ($user->image) {
                    $userImage = str_starts_with($user->image, 'data:image') 
                        ? $user->image 
                        : asset($user->image);
                }
            }
            // Check Manager/Supervisor
            elseif (Auth::guard('manager')->check()) {
                $user = Auth::guard('manager')->user();
                $userRole = $user->loginas ?? 'Manager';
                if ($user->image) {
                    $userImage = str_starts_with($user->image, 'data:image') 
                        ? $user->image 
                        : asset($user->image);
                }
            }
            // Check Intern
            elseif (Auth::guard('intern')->check()) {
                $user = Auth::guard('intern')->user();
                $userRole = 'Intern';
                // Interns may not have image field yet
                $userImage = asset('assets/img/branding/ezitech.png');
            }
            // Fallback - get admin for empty state
            else {
                $user = \App\Models\AdminAccount::first();
                $userRole = 'Admin';
                if ($user && $user->image) {
                    $userImage = str_starts_with($user->image, 'data:image') 
                        ? $user->image 
                        : asset($user->image);
                }
            }
            
            // Share data with all views
            $view->with('globalUser', $user)
                 ->with('globalUserRole', $userRole)
                 ->with('globalUserImage', $userImage);
        });
    }
}