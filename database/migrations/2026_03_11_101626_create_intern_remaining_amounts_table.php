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
        Schema::create('intern_remaining_amounts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->string('email')->unique('email');
            $table->string('contact');
            $table->decimal('remaining_amount', 10);
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_remaining_amounts');
    }
};
