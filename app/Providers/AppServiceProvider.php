<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
            if ($src !== null) {
                return [
                    'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src) ? 'template-customizer-core-css' : (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src) ? 'template-customizer-theme-css' : '')
                ];
            }
            return [];
        });


        // --- Global SMTP Configuration from DB ---
        try {
            if (Schema::hasTable('admin_settings')) {
                $settings = DB::table('admin_settings')->first();

                
                if ($settings && $settings->smtp_active_check == 1) {
    $config = [
        'transport'    => 'smtp',
        'host'         => $settings->smtp_host,
        'port'         => (int)$settings->smtp_port,
        'encryption'   => ($settings->smtp_port == 465) ? 'ssl' : 'tls',
        'username'     => $settings->smtp_email, // Gmail username is your email
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
            
        }
    }
}
