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
            $table->bigIncrements('id');
            $table->string('title');
            $table->json('content');
            $table->integer('manager_id')->index('offer_letter_templates_manager_id_foreign');
            $table->boolean('can_use_other_template')->default(false);
            $table->boolean('status')->default(false);
            $table->boolean('is_deleted')->default(false);
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
