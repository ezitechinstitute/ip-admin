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
        Schema::create('offer_letter_requests', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('offer_letter_id');
            $table->string('username', 100);
            $table->string('email', 100);
            $table->string('ezi_id', 50);
            $table->string('intern_status', 50)->nullable();
            $table->string('tech', 100)->nullable();
            $table->text('reason');
            $table->enum('status', ['pending', 'reject', 'accept'])->nullable()->default('pending');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_letter_requests');
    }
};
