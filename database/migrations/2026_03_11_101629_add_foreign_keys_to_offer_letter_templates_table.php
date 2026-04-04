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
        if (Schema::hasTable('offer_letter_templates')) {
            // Drop constraint first if it exists
            Schema::table('offer_letter_templates', function (Blueprint $table) {
                try { $table->dropForeign('offer_letter_templates_manager_id_foreign'); } catch (\Exception $e) {}
            });
            
            // Add constraint fresh
            Schema::table('offer_letter_templates', function (Blueprint $table) {
                $table->foreign(['manager_id'])->references(['manager_id'])->on('manager_accounts')->onUpdate('restrict')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_letter_templates', function (Blueprint $table) {
            $table->dropForeign('offer_letter_templates_manager_id_foreign');
        });
    }
};
