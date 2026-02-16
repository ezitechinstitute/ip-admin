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
        Schema::create('password_otp_resets', function (Blueprint $table) {
        $table->id();
        $table->string('email')->index(); 
        $table->string('otp');           // 6-digit numeric code
        $table->timestamp('created_at')->nullable(); 
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_otp_resets');
    }
};
