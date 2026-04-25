<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {

                if (!Schema::hasColumn('transactions', 'notes')) {
                    $table->text('notes')->nullable()->after('amount');
                }

                if (!Schema::hasColumn('transactions', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('notes');
                }

                if (!Schema::hasColumn('transactions', 'created_by_name')) {
                    $table->string('created_by_name')->nullable()->after('created_by');
                }

                if (!Schema::hasColumn('transactions', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {

            foreach (['notes', 'created_by', 'created_by_name', 'updated_at'] as $column) {
                if (Schema::hasColumn('transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};