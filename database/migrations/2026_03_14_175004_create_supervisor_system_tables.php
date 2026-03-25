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
        if (!Schema::hasTable('supervisor_activity_logs')) {
            Schema::create('supervisor_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->integer('supervisor_id');
                $table->string('action');
                $table->text('details')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('supervisor_notifications')) {
            Schema::create('supervisor_notifications', function (Blueprint $table) {
                $table->id();
                $table->integer('supervisor_id');
                $table->string('type');
                $table->string('eti_id')->nullable();
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisor_activity_logs');
        Schema::dropIfExists('supervisor_notifications');
    }
};
