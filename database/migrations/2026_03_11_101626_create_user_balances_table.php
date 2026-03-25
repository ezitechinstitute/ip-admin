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
        if (!Schema::hasTable('user_balances')) {
            Schema::create('user_balances', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('email')->unique('email');
                $table->decimal('balance', 10);
                $table->integer('year');
                $table->integer('month');
                $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_balances');
    }
};
