<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('approval_status')->default('pending')->after('status');
            $table->string('intern_id')->nullable()->after('intern_email');
            $table->string('technology')->nullable()->after('intern_id');
            $table->date('next_due_date')->nullable()->after('due_date');
            $table->text('notes')->nullable()->after('next_due_date');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'intern_id', 'technology', 'next_due_date', 'notes']);
        });
    }
};