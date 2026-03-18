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
            $table->bigIncrements('id');
            $table->string('system_logo')->nullable();
            $table->boolean('smtp_active_check')->default(false);
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_email')->nullable();
            $table->string('smtp_password')->nullable();
            $table->boolean('notify_intern_reg')->default(true);
            $table->boolean('notify_expense')->default(true);
            $table->integer('pagination_limit')->default(15);
            $table->integer('interview_timeout')->default(30);
            $table->integer('internship_duration')->default(6);
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
