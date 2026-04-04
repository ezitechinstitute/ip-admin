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
        if (!Schema::hasTable('intern_feedback')) {
            Schema::create('intern_feedback', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('eti_id');
                $table->text('feedback_text');
                $table->timestamp('created_at')->useCurrent();
                $table->enum('status', ['Open', 'Resolved'])->nullable()->default('Open');
                $table->timestamp('resolved_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_feedback');
    }
};
