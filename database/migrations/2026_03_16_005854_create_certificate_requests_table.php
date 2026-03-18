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
        Schema::create('certificate_requests', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_request_id')->unique();
            $table->unsignedBigInteger('intern_id');
            $table->string('intern_name', 150);
            $table->string('email', 150);
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->enum('certificate_type', ['internship', 'course_completion'])->default('internship');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('intern_id');
            $table->index('manager_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_requests');
    }
};
