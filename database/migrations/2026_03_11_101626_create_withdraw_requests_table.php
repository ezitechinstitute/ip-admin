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
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->integer('req_id', true);
            $table->string('eti_id')->index('reqkey2');
            $table->integer('req_by')->index('reqkey1');
            $table->string('bank');
            $table->string('ac_no');
            $table->string('ac_name');
            $table->text('description');
            $table->date('date');
            $table->double('amount');
            $table->integer('req_status')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_requests');
    }
};
