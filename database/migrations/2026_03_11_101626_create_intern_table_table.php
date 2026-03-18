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
        Schema::create('intern_table', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->string('email');
            $table->string('city');
            $table->string('phone');
            $table->string('cnic');
            $table->string('gender');
            $table->text('image');
            $table->string('join_date');
            $table->string('birth_date');
            $table->string('university');
            $table->string('country');
            $table->string('interview_type');
            $table->string('technology');
            $table->string('duration');
            $table->string('status')->default('Interview');
            $table->integer('supervisor_id')->nullable()->index();
            $table->string('intern_type');
            $table->string('interview_date')->default('Onsite');
            $table->string('interview_time')->default('Onsite');
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_table');
    }
};
