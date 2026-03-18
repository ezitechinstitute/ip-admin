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
        Schema::create('intern_leaves', function (Blueprint $table) {
            $table->integer('leave_id', true);
            $table->string('eti_id')->index('etikey');
            $table->string('name');
            $table->string('email');
            $table->date('from_date');
            $table->date('to_date');
            $table->text('reason');
            $table->string('technology');
            $table->string('intern_type');
            $table->integer('days');
            $table->boolean('leave_status')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_leaves');
    }
};
