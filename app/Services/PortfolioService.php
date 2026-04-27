<?php

namespace App\Services;

use App\Helpers\Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

class PortfolioService
{
    /**
     * Get portfolio statistics with rounded numbers
     */
    public function getPortfolioStats($intern): array
    {
        $stats = $this->getProfileStats($intern->eti_id);
        
        // Round all stats to whole numbers
        return [
            'total_tasks' => (int) round($stats['total_tasks']),
            'completed_tasks' => (int) round($stats['completed_tasks']),
            'total_projects' => (int) round($stats['total_projects']),
            'completed_projects' => (int) round($stats['completed_projects']),
        ];
    }

    /**
     * Get stat items for display
     */
    public function getStatItems(array $stats): array
    {
        return [
            [
                'icon' => 'bi bi-list-check',
                'value' => $stats['total_tasks'],
                'label' => 'Total Tasks',
                'color' => '#3b82f6'
            ],
            [
                'icon' => 'bi bi-check-circle',
                'value' => $stats['completed_tasks'],
                'label' => 'Tasks Done',
                'color' => '#10b981'
            ],
            [
                'icon' => 'bi bi-briefcase',
                'value' => $stats['total_projects'],
                'label' => 'Total Projects',
                'color' => '#f59e0b'
            ],
            [
                'icon' => 'bi bi-trophy',
                'value' => $stats['completed_projects'],
                'label' => 'Projects Done',
                'color' => '#8b5cf6'
            ],
        ];
    }

    /**
     * Calculate internship progress
     */
    public function calculateInternshipProgress($intern): array
    {
        $startDate = Carbon::parse($intern->start_date ?? now());
        $endDate = $startDate->copy()->addMonths(6);
        $totalDays = (int) round($startDate->diffInDays($endDate));
        $elapsedDays = (int) round($startDate->diffInDays(Carbon::now()));
        $remainingDays = max(0, (int) round($totalDays - $elapsedDays));
        $progress = $totalDays > 0 ? (int) round(($elapsedDays / $totalDays) * 100) : 0;

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'elapsed_days' => $elapsedDays,
            'remaining_days' => $remainingDays,
            'progress_percent' => $progress,
        ];
    }

    /**
     * Calculate task completion rate
     */
    public function calculateTaskRate(array $stats): int
    {
        return $stats['total_tasks'] > 0 
            ? (int) round(($stats['completed_tasks'] / $stats['total_tasks']) * 100) 
            : 0;
    }

    /**
     * Calculate project completion rate
     */
    public function calculateProjectRate(array $stats): int
    {
        return $stats['total_projects'] > 0 
            ? (int) round(($stats['completed_projects'] / $stats['total_projects']) * 100) 
            : 0;
    }

    /**
     * Get achievement badges
     */
    public function getAchievementBadges(array $stats): array
    {
        $badges = [
            [
                'name' => 'Task Starter',
                'icon' => 'bi-rocket-takeoff-fill',
                'color' => 'primary',
                'earned' => ($stats['completed_tasks'] ?? 0) >= 1
            ],
            [
                'name' => 'Task Master',
                'icon' => 'bi-trophy-fill',
                'color' => 'warning',
                'earned' => ($stats['completed_tasks'] ?? 0) >= 5
            ],
            [
                'name' => 'Project Builder',
                'icon' => 'bi-briefcase-fill',
                'color' => 'success',
                'earned' => ($stats['completed_projects'] ?? 0) >= 1
            ],
            [
                'name' => 'Code Champion',
                'icon' => 'bi-code-square',
                'color' => 'info',
                'earned' => ($stats['completed_projects'] ?? 0) >= 2
            ],
            [
                'name' => 'Perfect Attendance',
                'icon' => 'bi-calendar-check-fill',
                'color' => 'purple',
                'earned' => ($stats['total_tasks'] ?? 0) >= 10
            ],
            [
                'name' => 'Productivity King',
                'icon' => 'bi-graph-up',
                'color' => 'danger',
                'earned' => ($stats['completed_tasks'] ?? 0) >= 10
            ],
        ];

        return [
            'badges' => $badges,
            'earned_count' => collect($badges)->where('earned', true)->count(),
            'total_count' => count($badges),
        ];
    }

    /**
     * Get profile image
     */
    public function getProfileImage($intern): string
    {
        return Helpers::getProfileImage($intern);
    }

    /**
     * Get intern skills
     */
    public function getInternSkills(int $internId): Collection
    {
        if (!Schema::hasTable('intern_skills')) {
            return collect([]);
        }
        
        try {
            $skills = DB::table('intern_skills')
                ->where('intern_id', $internId)
                ->pluck('skill');
            
            return $skills->filter(function($skill) {
                return !empty(trim($skill));
            })->values();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Get approved projects
     */
    public function getApprovedProjects(string $etiId): Collection
    {
        if (!Schema::hasTable('intern_projects')) {
            return collect([]);
        }
        
        try {
            $query = DB::table('intern_projects')
                ->where('eti_id', $etiId)
                ->where('pstatus', 'approved');
            
            if (Schema::hasColumn('intern_projects', 'created_at')) {
                $query->orderBy('created_at', 'desc');
            }
            
            return $query->limit(10)->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Get approved certificates
     */
    public function getApprovedCertificates(int $internId): Collection
    {
        if (!Schema::hasTable('generated_certificates')) {
            return collect([]);
        }
        
        try {
            $query = DB::table('generated_certificates')
                ->where('intern_id', $internId)
                ->where('status', 'approved');
            
            if (Schema::hasColumn('generated_certificates', 'created_at')) {
                $query->orderBy('created_at', 'desc');
            }
            
            return $query->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Get profile statistics from database
     */
    private function getProfileStats(string $etiId): array
    {
        $stats = [
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'total_projects' => 0,
            'completed_projects' => 0,
        ];
        
        try {
            if (Schema::hasTable('intern_tasks')) {
                $stats['total_tasks'] = DB::table('intern_tasks')
                    ->where('eti_id', $etiId)
                    ->count();
                    
                if (Schema::hasColumn('intern_tasks', 'task_status')) {
                    $stats['completed_tasks'] = DB::table('intern_tasks')
                        ->where('eti_id', $etiId)
                        ->where('task_status', 'approved')
                        ->count();
                }
            }
            
            if (Schema::hasTable('intern_projects')) {
                $stats['total_projects'] = DB::table('intern_projects')
                    ->where('eti_id', $etiId)
                    ->count();
                    
                if (Schema::hasColumn('intern_projects', 'pstatus')) {
                    $stats['completed_projects'] = DB::table('intern_projects')
                        ->where('eti_id', $etiId)
                        ->where('pstatus', 'approved')
                        ->count();
                }
            }
        } catch (\Exception $e) {
            // Tables might not exist yet
        }
        
        return $stats;
    }
}