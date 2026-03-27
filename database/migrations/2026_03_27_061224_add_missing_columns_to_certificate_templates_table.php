<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('certificate_templates', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            if (!Schema::hasColumn('certificate_templates', 'content')) {
                $table->text('content')->nullable()->after('title');
            }
            if (!Schema::hasColumn('certificate_templates', 'certificate_type')) {
                $table->enum('certificate_type', ['internship', 'course_completion'])->default('internship')->after('content');
            }
            if (!Schema::hasColumn('certificate_templates', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable()->after('certificate_type');
            }
            if (!Schema::hasColumn('certificate_templates', 'status')) {
                $table->boolean('status')->default(1)->after('manager_id');
            }
            if (!Schema::hasColumn('certificate_templates', 'is_deleted')) {
                $table->boolean('is_deleted')->default(0)->after('status');
            }
            
            // Drop old columns if they exist
            if (Schema::hasColumn('certificate_templates', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('certificate_templates', 'template_path')) {
                $table->dropColumn('template_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->dropColumn(['title', 'content', 'certificate_type', 'manager_id', 'status', 'is_deleted']);
        });
    }
};
