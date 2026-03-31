<?php

namespace Database\Seeders;

use App\Models\CertificateRequest;
use App\Models\CertificateTemplate;
use App\Models\ManagersAccount;
use App\Models\ManagerRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = ManagersAccount::firstOrCreate(
            ['email' => 'cert-manager@example.com'],
            [
                'eti_id' => 'ETI-MAN-001',
                'image' => '',
                'name' => 'Certificate Manager',
                'email' => 'cert-manager@example.com',
                'contact' => '0000000000',
                'join_date' => now()->toDateString(),
                'password' => bcrypt('password'),
                'comission' => 10,
                'department' => 'Operations',
                'status' => 1,
                'loginas' => 'Manager',
                'emergency_contact' => 0,
            ]
        );

        // Permissions for certificate and related manager pages
        $permissions = [
            'view_manager_certificate_templates',
            'add_new_manager_certificate_templates',
            'edit_manager_certificate_templates',
            'delete_manager_certificate_templates',
            'view_manager_certificate_requests',
            'approve_manager_certificate_requests',
            'reject_manager_certificate_requests',
            'view_manager_offer_letter_template',
            'view_manager_offer_letter_request',
        ];

        ManagerRole::where('manager_id', $manager->manager_id)->delete();
        foreach ($permissions as $perm) {
            ManagerRole::create(['manager_id' => $manager->manager_id, 'permission_key' => $perm]);
        }

        CertificateTemplate::updateOrCreate(
            ['title' => 'Sample Internship Completion Certificate', 'certificate_type' => 'internship'],
            [
                'content' => '<div style="padding:40px; border:1px solid #ddd; font-family:Arial"><h1>Internship Completion Certificate</h1><p>This certifies that <strong>{{name}}</strong> has successfully completed the internship.</p><p>Date: {{date}}</p></div>',
                'manager_id' => $manager->manager_id,
                'status' => 1,
                'is_deleted' => 0,
            ]
        );

        CertificateTemplate::updateOrCreate(
            ['title' => 'Sample Course Completion Certificate', 'certificate_type' => 'course_completion'],
            [
                'content' => '<div style="padding:40px; border:1px solid #ddd; font-family:Arial"><h1>Course Completion Certificate</h1><p>This certifies that <strong>{{name}}</strong> has successfully completed the course.</p><p>Date: {{date}}</p></div>',
                'manager_id' => $manager->manager_id,
                'status' => 1,
                'is_deleted' => 0,
            ]
        );

        // Seed several request states for testing
        CertificateRequest::updateOrCreate(
            ['certificate_request_id' => 'CERT-TEST-001'],
            [
                'intern_id' => 1,
                'intern_name' => 'Test Intern',
                'email' => 'intern@example.com',
                'manager_id' => $manager->manager_id,
                'certificate_type' => 'internship',
                'status' => 'pending',
                'reason' => null,
            ]
        );

        CertificateRequest::updateOrCreate(
            ['certificate_request_id' => 'CERT-TEST-002'],
            [
                'intern_id' => 2,
                'intern_name' => 'Jane Doe',
                'email' => 'jane.doe@example.com',
                'manager_id' => $manager->manager_id,
                'certificate_type' => 'course_completion',
                'status' => 'approved',
                'reason' => null,
            ]
        );

        CertificateRequest::updateOrCreate(
            ['certificate_request_id' => 'CERT-TEST-003'],
            [
                'intern_id' => 3,
                'intern_name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'manager_id' => $manager->manager_id,
                'certificate_type' => 'internship',
                'status' => 'rejected',
                'reason' => 'Incomplete hours',
            ]
        );
    }
}
