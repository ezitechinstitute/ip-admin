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
        Schema::create('offer_letter_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->json('content');
            $table->integer('manager_id');
            $table->boolean('can_use_other_template')->default(0);
            $table->boolean('status')->default(0);
            $table->boolean('is_deleted')->default(0);
            
            $table->foreign('manager_id')->references('manager_id')->on('manager_accounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_letter_templates');
    }
};
