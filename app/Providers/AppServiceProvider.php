<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;
use App\Models\ManagerRole;
use App\Models\SupervisorRole;
use App\Models\InternProject;
use App\Observers\InternProjectObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // =========================
        // PROJECT CHAT OBSERVER
        // =========================
        // This automatically creates a chat room when a new project is saved.
        InternProject::observe(InternProjectObserver::class);


        // =========================
        // VITE CONFIG (UNCHANGED)
        // =========================
        Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
            if ($src !== null) {
                return [
                    'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src)
                        ? 'template-customizer-core-css'
                        : (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src)
                            ? 'template-customizer-theme-css'
                            : '')
                ];
            }
            return [];
        });

        // =========================
        // PERMISSION SYSTEM (FINAL FIX)
        // =========================
        Gate::define('check-privilege', function ($user, $permissionKey) {

            // ❗ Always use $user from Gate
            if (!$user) {
                return false;
            }

            // =========================
            // SUPERVISOR
            // =========================
            if ($user->loginas === 'Supervisor') {

                return SupervisorRole::where('supervisor_id', $user->manager_id)
                    ->where('permission_key', $permissionKey)
                    ->exists();
            }

            // =========================
            // MANAGER
            // =========================
            if ($user->loginas === 'Manager') {

                return ManagerRole::where('manager_id', $user->manager_id)
                    ->where('permission_key', $permissionKey)
                    ->exists();
            }

            return false;
        });

        // =========================
        // SMTP CONFIG (UNCHANGED)
        // =========================
        try {
            if (Schema::hasTable('admin_settings')) {

                $settings = DB::table('admin_settings')->first();

                if ($settings && $settings->smtp_active_check == 1) {

                    $config = [
                        'transport'    => 'smtp',
                        'host'         => $settings->smtp_host,
                        'port'         => (int)$settings->smtp_port,
                        'encryption'   => ($settings->smtp_port == 465) ? 'ssl' : 'tls',
                        'username'     => $settings->smtp_email,
                        'password'     => $settings->smtp_password,
                        'timeout'      => null,
                        'auth_mode'    => null,
                    ];

                    Config::set('mail.mailers.smtp', $config);
                    Config::set('mail.from.address', $settings->smtp_email);
                    Config::set('mail.from.name', 'Ezitech Learning Institute');
                }
            }
        } catch (\Exception $e) {
            // Silent fail (safe)
        }
    }
}