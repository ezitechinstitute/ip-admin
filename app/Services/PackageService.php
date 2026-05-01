<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PackageService
{
    const CACHE_KEY = 'internship_packages';
    const CACHE_TTL = 86400;

    public static function getAll(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $packages = config('packages.internships', []);
            
            return collect($packages)
                ->filter(fn($pkg) => $pkg['active'] ?? true)
                ->map(fn($pkg, $slug) => array_merge($pkg, ['slug' => $slug]))
                ->values()
                ->toArray();
        });
    }

    public static function formatAmount(float $amount): string
    {
        return 'PKR ' . number_format($amount);
    }
}