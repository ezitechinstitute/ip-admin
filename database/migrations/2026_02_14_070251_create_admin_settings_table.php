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
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            
            // 1. System Branding
            $table->string('system_logo')->nullable();
            
            // 2. Email Configuration (SMTP)
            $table->boolean('smtp_active_check')->default(0);
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_email')->nullable();
            $table->string('smtp_password')->nullable();
            
            // 3. Notification Rules
            $table->boolean('notify_intern_reg')->default(1);
            $table->boolean('notify_expense')->default(1);
            
            // 4. Advanced System Presets
            $table->integer('pagination_limit')->default(15);
            $table->integer('interview_timeout')->default(30); // Save as minutes
            $table->integer('internship_duration')->default(6); // Save as months
            
            // 5. Dynamic Categories & Permissions (JSON)
            // JSON use karne ka faida ye hai ke future mein asani se expand ho sakega
            $table->json('expense_categories')->nullable(); 
            $table->json('export_permissions')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};