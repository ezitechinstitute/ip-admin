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
        if (!Schema::hasTable('manager_payout_requests')) {
            Schema::create('manager_payout_requests', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('manager_id')->index('manager_payout_requests_manager_id_foreign');
                $table->decimal('requested_amount', 15);
                $table->date('period_start')->nullable();
                $table->date('period_end')->nullable();
                $table->text('description')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->string('admin_remarks')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_payout_requests');
    }
};
