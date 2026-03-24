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
        Schema::create('generated_certificates', function (Blueprint $table) {

            $table->id();

            $table->integer('intern_id');

            $table->unsignedBigInteger('template_id');

            $table->string('certificate_path')->nullable();

            $table->enum('status', ['pending','approved','rejected'])->default('pending');

            $table->unsignedBigInteger('approved_by')->nullable();

            $table->timestamps();

            $table->foreign('intern_id')
                ->references('int_id')
                ->on('intern_accounts')
                ->onDelete('cascade');

            $table->foreign('template_id')
                ->references('id')
                ->on('certificate_templates')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_certificates');
    }
};
