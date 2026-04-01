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
        if (!Schema::hasTable('intern_notifications')) {
            Schema::create('intern_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('intern_id');
                $table->string('title');
                $table->text('message');
                $table->string('type')->nullable(); // task, invoice, certificate, feedback
                $table->string('link')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamps();

                // Indexes for performance
                $table->index('intern_id');
                $table->index('is_read');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_notifications');
    }
};