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
        Schema::create('office_location', function (Blueprint $table) {
            $table->integer('id', true);
            $table->double('lati');
            $table->double('longi');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('udated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_location');
    }
};
