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
        if (!Schema::hasTable('generated_certificates')) {
            Schema::create('generated_certificates', function (Blueprint $table) {
                $table->id();
                $table->integer('intern_id');
                $table->integer('template_id');  // Changed to integer to match your existing structure
                $table->string('certificate_path')->nullable();
                $table->string('status')->default('pending');  // Use string instead of enum
                $table->integer('approved_by')->nullable();
                $table->timestamps();

                // Only add foreign key for intern_accounts (which exists)
                $table->foreign('intern_id')
                    ->references('int_id')
                    ->on('intern_accounts')
                    ->onDelete('cascade');
                
                // Just add index for template_id (no foreign key)
                $table->index('template_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_certificates');
    }
};