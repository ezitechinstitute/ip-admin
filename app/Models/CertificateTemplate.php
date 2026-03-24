<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneratedCertificate;

/**
 * CertificateTemplate Model
 * 
 * Represents HTML-based certificate templates with dynamic variable support.
 * Part of the consolidated certificate management system.
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 * ATTRIBUTES:
 * ─────────────
 * @property int         $id
 * @property string      $title           Certificate template name
 * @property string      $content         HTML content with template variables
 * @property string      $certificate_type 'internship' or 'course_completion'
 * @property int         $manager_id      Manager who created this template
 * @property boolean     $status          1=active, 0=inactive
 * @property boolean     $is_deleted      Soft delete flag (data retention)
 * @property timestamp   $created_at
 * @property timestamp   $updated_at
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 * TEMPLATE VARIABLES (for dynamic content):
 * ──────────────────────────────────────────
 * • {{name}}              → Intern full name
 * • {{email}}             → Intern email address
 * • {{certificate_type}}  → Certificate type (Internship / Course Completion)
 * • {{date}}              → Current date (d M Y format)
 * 
 * EXAMPLE CONTENT:
 * ────────────────
 * <html>
 *   <body>
 *     <h1>Certificate of {{certificate_type}}</h1>
 *     <p>This certifies that {{name}} has completed...</p>
 *     <p>Date: {{date}}</p>
 *   </body>
 * </html>
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 * SOFT DELETE:
 * ────────────
 * Uses is_deleted flag instead of forceDelete() for data retention.
 * Soft-deleted templates are excluded from queries automatically.
 * 
 * STATUS vs IS_DELETED:
 * ─────────────────────
 * • status=1, is_deleted=0  → Active template (available for use)
 * • status=0, is_deleted=0  → Inactive template (archived but not deleted)
 * • status=any, is_deleted=1 → Deleted template (hidden from all queries)
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 * RELATIONSHIPS:
 * ───────────────
 * • certificates() → HasMany GeneratedCertificate
 *   Used by GeneratedCertificateController (if referenced in future)
 * 
 * MANAGED BY (Consolidated):
 * ──────────────────────────
 * • CertificateController::templates()        → List templates
 * • CertificateController::storeTemplate()    → Create template
 * • CertificateController::updateTemplate()   → Update template
 * • CertificateController::destroyTemplate()  → Soft delete template
 * • CertificateController::previewTemplate()  → Generate PDF preview
 * 
 * Note: Previous CertificateTemplateController classes (manager & admin)
 * have been consolidated into the unified CertificateController.
 * 
 * @author System
 * @version 2.0 (Consolidated)
 * @since 2024
 */
class CertificateTemplate extends Model
{
    protected $table = 'certificate_templates';

    protected $fillable = [
        'title',
        'content',
        'certificate_type',
        'manager_id',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];
    
    public function certificates()
    {
        return $this->hasMany(GeneratedCertificate::class,'template_id');
    }
}