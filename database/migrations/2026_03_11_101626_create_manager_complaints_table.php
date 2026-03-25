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
        if (!Schema::hasTable('manager_complaints')) {
            Schema::create('manager_complaints', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('eti_id')->index('eti_id');
                $table->string('complaint_name');
                $table->text('complaint_text');
                $table->timestamp('created_at')->useCurrent();
                $table->enum('status', ['Pending', 'Accepted', 'Rejected'])->default('Pending');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_complaints');
    }
};
