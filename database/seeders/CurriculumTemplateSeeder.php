<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurriculumTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $curriculums = [
            [
                'technology' => 'Laravel',
                'task_title' => 'Project Architecture Design',
                'task_description' => 'Create ERD, Database schema, and choose architectural patterns.',
                'task_duration_days' => 2,
                'task_mark' => 10,
                'milestone_title' => 'Milestone 1: Planning',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'technology' => 'Laravel',
                'task_title' => 'Auth System & Middleware',
                'task_description' => 'Implement JWT or Breeze, and secure all routes using middleware.',
                'task_duration_days' => 3,
                'task_mark' => 15,
                'milestone_title' => 'Milestone 2: Core Features',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'technology' => 'Laravel',
                'task_title' => 'API Endpoint Development',
                'task_description' => 'Build RESTful APIs for all resources.',
                'task_duration_days' => 5,
                'task_mark' => 20,
                'milestone_title' => 'Milestone 2: Core Features',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'technology' => 'React',
                'task_title' => 'Component Structure & Routing',
                'task_description' => 'Setup React Router and atomic design components.',
                'task_duration_days' => 3,
                'task_mark' => 10,
                'milestone_title' => 'Phase 1: Foundation',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'technology' => 'React',
                'task_title' => 'State Management (Redux/Context)',
                'task_description' => 'Implement global store for application state.',
                'task_duration_days' => 4,
                'task_mark' => 20,
                'milestone_title' => 'Phase 2: Logic',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ];

        DB::table('curriculum_templates')->insert($curriculums);
    }
}
